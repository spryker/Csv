<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator;

use Generated\Shared\Calculation\DiscountItemsInterface;
use Generated\Shared\Calculation\TotalsInterface;
use Generated\Shared\DiscountCalculationConnector\DiscountInterface;
use Generated\Shared\DiscountCalculationConnector\ExpenseInterface;
use Generated\Shared\DiscountCalculationConnector\ItemInterface;
use Generated\Shared\DiscountCalculationConnector\ProductOptionInterface;
use Generated\Shared\Transfer\DiscountTotalsTransfer;
use Generated\Shared\Transfer\DiscountTotalItemTransfer;
use Generated\Shared\Sales\OrderItemsInterface;
use SprykerFeature\Zed\Calculation\Business\Model\CalculableInterface;

class DiscountTotalsCalculator implements DiscountTotalsCalculatorInterface
{

    /**
     * @param TotalsInterface $totalsTransfer
     * @param CalculableInterface $discountableContainer
     * @param $discountableContainers
     */
    public function recalculateTotals(
        TotalsInterface $totalsTransfer,
        CalculableInterface $discountableContainer,
        $discountableContainers
    ) {
        $discountTransfer = $this->createDiscountTransfer($discountableContainer, $discountableContainers);
        $totalsTransfer->setDiscount($discountTransfer);
    }

    /**
     * @param CalculableInterface $discountableContainer
     * @param $discountableItems
     *
     * @return array|int
     */
    public function calculateDiscount(
        CalculableInterface $discountableContainer,
        $discountableItems
    ) {
        $discountAmount = 0;

        if ($discountableItems instanceof OrderItemsInterface) {
            $discountableItems = $discountableItems->getOrderItems();
        }

        foreach ($discountableItems as $itemTransfer) {
            $discountAmount += $this->calculateItemDiscountAmount($itemTransfer);
        }

        $discountAmount += $this->sumTotalExpenseDiscounts($discountableContainer);

        return $discountAmount;
    }

    /**
     * @param ItemInterface $itemTransfer
     *
     * @return int
     */
    protected function calculateItemDiscountAmount(ItemInterface $itemTransfer)
    {
        $itemDiscountAmount = 0;

        $itemDiscountAmount += $this->sumItemDiscounts($itemTransfer->getDiscounts());
        $itemDiscountAmount += $this->sumItemExpenseDiscounts($itemTransfer->getExpenses());
        $itemDiscountAmount += $this->sumOptionDiscounts($itemTransfer->getProductOptions());

        $itemDiscountAmount = $itemDiscountAmount * $itemTransfer->getQuantity();

        return $itemDiscountAmount;
    }

    /**
     * @param CalculableInterface $discountableContainer
     * @param $discountableContainers
     *
     * @return DiscountTotalsTransfer
     */
    protected function createDiscountTransfer(
        CalculableInterface $discountableContainer,
        $discountableContainers
    ) {
        $discountTransfer = new DiscountTotalsTransfer();
        $discountTransfer->setTotalAmount($this->calculateDiscount($discountableContainer, $discountableContainers));

        foreach ($this->sumDiscountItems($discountableContainer, $discountableContainers) as $discountTotalItem) {
            $discountTransfer->addDiscountItem($discountTotalItem);
        }

        return $discountTransfer;
    }

    /**
     * @param CalculableInterface $discountableContainer
     * @param $discountableItems
     *
     * @return array
     */
    protected function sumDiscountItems(
        CalculableInterface $discountableContainer,
        $discountableItems
    ) {
        $orderExpenseItems = [];

        foreach ($discountableContainer->getCalculableObject()->getDiscounts() as $discount) {
            $this->transformDiscountToDiscountTotalItemInArray($discount, $orderExpenseItems);
        }

        foreach ($discountableContainer->getCalculableObject()->getExpenses() as $expenses) {
            foreach ($expenses->getDiscounts() as $discount) {
                $this->transformDiscountToDiscountTotalItemInArray($discount, $orderExpenseItems);
            }
        }

        foreach ($discountableItems as $container) {
            foreach ($container->getDiscounts() as $discount) {
                $this->transformDiscountToDiscountTotalItemInArray($discount, $orderExpenseItems);
            }

            foreach ($container->getProductOptions() as $option) {
                foreach ($option->getDiscounts() as $discount) {
                    $this->transformDiscountToDiscountTotalItemInArray($discount, $orderExpenseItems);
                }
            }

            foreach ($container->getExpenses() as $expenses) {
                foreach ($expenses->getDiscounts() as $discount) {
                    $this->transformDiscountToDiscountTotalItemInArray($discount, $orderExpenseItems);
                }
            }
        }

        return $orderExpenseItems;
    }

    /**
     * @param DiscountInterface $discountTransfer
     * @param array $arrayOfExpenseTotalItems
     */
    protected function transformDiscountToDiscountTotalItemInArray(
        DiscountInterface $discountTransfer,
        array &$arrayOfExpenseTotalItems
    ) {
        if (!isset($arrayOfExpenseTotalItems[$discountTransfer->getDisplayName()])) {
            $discountTotalItemTransfer = $this->getDiscountTotalItem();
            $discountTotalItemTransfer->setName($discountTransfer->getDisplayName());
        } else {
            $discountTotalItemTransfer = $arrayOfExpenseTotalItems[$discountTransfer->getDisplayName()];
        }

        $this->setUsedCodes($discountTotalItemTransfer, $discountTransfer);

        $discountTotalItemTransfer->setAmount($discountTotalItemTransfer->getAmount() + $discountTransfer->getAmount());
        $arrayOfExpenseTotalItems[$discountTransfer->getDisplayName()] = $discountTotalItemTransfer;
    }

    /**
     * @param DiscountTotalItemTransfer $discountTotalItemTransfer
     * @param DiscountInterface         $discountTransfer
     */
    protected function setUsedCodes(
        DiscountTotalItemTransfer $discountTotalItemTransfer,
        DiscountInterface $discountTransfer
    ) {
        $storedCodes = (array) $discountTotalItemTransfer->getCodes();
        foreach ($discountTransfer->getUsedCodes() as $code) {
            if (!in_array($code, $storedCodes)) {
                $discountTotalItemTransfer->addCode($code);
            }
        }
    }

    /**
     * @return DiscountTotalItemTransfer
     */
    protected function getDiscountTotalItem()
    {
        return new DiscountTotalItemTransfer();
    }

    /**
     * @param \ArrayObject|DiscountItemsInterface $discounts
     *
     * @return int
     */
    protected function sumItemDiscounts(\ArrayObject $discounts)
    {
        $discountAmount = 0;

        if ($discounts instanceof DiscountItemsInterface) {
            $discounts = $discounts->getDiscounts();
        }

        foreach ($discounts as $discount) {
            $discountAmount += $discount->getAmount();
        }

        return $discountAmount;
    }

    /**
     * @param \ArrayObject|ExpenseInterface[] $expenses
     *
     * @return int
     */
    protected function sumItemExpenseDiscounts(\ArrayObject $expenses)
    {
        $discountAmount = 0;

        foreach ($expenses as $expense) {
            if ($expense instanceof ExpenseInterface) {
                foreach ($expense->getDiscounts() as $discount) {
                    $discountAmount += $discount->getAmount();
                }
            }
        }

        return $discountAmount;
    }

    /**
     * @param \ArrayObject|ProductOptionInterface[] $options
     *
     * @return int
     */
    protected function sumOptionDiscounts(\ArrayObject $options)
    {
        $discountAmount = 0;

        foreach ($options as $option) {
            if ($option instanceof ProductOptionInterface) {
                foreach ($option->getDiscounts() as $discount) {
                    $discountAmount += $discount->getAmount();
                }
            }
        }

        return $discountAmount;
    }

    /**
     * @param CalculableInterface $discountableContainer
     *
     * @return int
     */
    protected function sumTotalExpenseDiscounts(CalculableInterface $discountableContainer)
    {
        $discountAmount = 0;

        foreach ($discountableContainer->getCalculableObject()->getExpenses() as $expense) {
            foreach ($expense->getDiscounts() as $discount) {
                $discountAmount += $discount->getAmount();
            }
        }

        return $discountAmount;
    }

}
