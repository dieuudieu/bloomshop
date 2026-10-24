<?php
// model/GioHangModel.php - Đóng gói toàn bộ nghiệp vụ giỏ hàng (Shopping Cart) theo OOP
class GioHangModel
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Lấy toàn bộ danh sách sản phẩm trong giỏ hàng
     */
    public function getItems()
    {
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Đếm tổng số lượng bông hoa / sản phẩm trong giỏ
     */
    public function getItemCount()
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += (int)($item['qty'] ?? 1);
        }
        return $count;
    }

    /**
     * Tính tổng tiền tạm tính của giỏ hàng (chưa trừ voucher)
     */
    public function getSubtotal()
    {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $subtotal += (float)$item['price'] * (int)$item['qty'];
        }
        return $subtotal;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addItem($id, $qty, $product)
    {
        $id = (int)$id;
        $qty = max(1, (int)$qty);

        $price = (!empty($product['is_sale']) && !empty($product['sale_price'])) 
            ? (float)$product['sale_price'] 
            : (float)$product['price'];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $price,
                'image' => $product['image'],
                'qty' => $qty
            ];
        }
    }

    /**
     * Cập nhật số lượng (+ / -)
     */
    public function updateItemQty($id, $action)
    {
        $id = (int)$id;
        if (isset($_SESSION['cart'][$id])) {
            if ($action === 'increase') {
                $_SESSION['cart'][$id]['qty']++;
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$id]['qty']--;
                if ($_SESSION['cart'][$id]['qty'] <= 0) {
                    $this->removeItem($id);
                }
            }
        }
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng
     */
    public function removeItem($id)
    {
        $id = (int)$id;
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng và voucher đang áp dụng
     */
    public function clear()
    {
        unset($_SESSION['cart']);
        unset($_SESSION['voucher']);
    }

    /**
     * Lấy voucher đang áp dụng
     */
    public function getVoucher()
    {
        return $_SESSION['voucher'] ?? null;
    }

    /**
     * Áp dụng voucher
     */
    public function setVoucher($voucherData)
    {
        $_SESSION['voucher'] = $voucherData;
    }

    /**
     * Bỏ áp dụng voucher
     */
    public function removeVoucher()
    {
        unset($_SESSION['voucher']);
    }

    /**
     * Tính toán số tiền được giảm giá theo voucher hiện tại
     */
    public function calculateDiscount($subtotal)
    {
        $discount_amount = 0;
        $voucher_code = null;
        $voucher_id = null;

        if (isset($_SESSION['voucher'])) {
            $vc = $_SESSION['voucher'];
            $voucher_code = $vc['code'];
            $voucher_id = $vc['id'] ?? null;

            if ($vc['discount_type'] === 'percent') {
                $discount_amount = ($subtotal * (float)$vc['discount_value']) / 100;
            } else {
                $discount_amount = (float)$vc['discount_value'];
            }

            if ($discount_amount > $subtotal) {
                $discount_amount = $subtotal;
            }
        }

        return [
            'amount' => $discount_amount,
            'code' => $voucher_code,
            'id' => $voucher_id
        ];
    }

    /**
     * Tính tổng số tiền thanh toán cuối cùng
     */
    public function calculateTotal()
    {
        $subtotal = $this->getSubtotal();
        $discount = $this->calculateDiscount($subtotal);
        return max(0, $subtotal - $discount['amount']);
    }
}
