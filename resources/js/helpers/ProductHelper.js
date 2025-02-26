class CartHelper {
    getColorForStatus(status) {
        switch (status) {
            case 'Необроблені':
                return 'text-gray-400 text-light';
            case 'Успішно':
                return 'text-black-400 text-light';
            case 'Потребує оплати':
                return 'text-red-400 font-bold';
            default:
                return 'text-green-400 text-light';
        }
    }
}

export default new CartHelper();
