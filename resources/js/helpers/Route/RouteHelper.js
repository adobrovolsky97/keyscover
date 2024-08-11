import router from "../../router.js";

class RouteHelper {
    updateQueryParams(params) {
        router.push({query: params});
    }
}

export default new RouteHelper();
