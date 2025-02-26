class CartHelper {
    getColorForStatus(status) {
        switch (status) {
            case 'Необроблені':
                return 'text-gray-400';
            case 'Успішно':
                return 'text-black-400';
            case 'Потребує оплати':
                return 'text-red-400 font-bold';
            default:
                return 'text-green-400';
        }
    }
}

export default new CartHelper();
