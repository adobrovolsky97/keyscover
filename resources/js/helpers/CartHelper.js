
class CartHelper {
    updateCartQuantity(cartQty, product) {
        if (cartQty % product.cart_increment_step !== 0) {
            cartQty = parseInt(cartQty) + parseInt(product.cart_increment_step - (cartQty % product.cart_increment_step));
        }

        if (product.left_in_stock <= cartQty) {
            cartQty = product.left_in_stock - (product.left_in_stock % product.cart_increment_step);
        }

        return cartQty;
    }
}

export default new CartHelper();
