export default {
    getcategoryList: (state) => {
        return state.categoryList;
    },

    getDetails: (state) => {
        return state.dataDetails;
    },

    getSelectedCategory: (state) => {
        return state.selectedCategory;
    },

    getProductList: (state) => {
        return state.productList;
    },

    getselectedMenuProductIds: (state) => {
        return state.selectedMenuProductIds;
    },

    getselectedMenuProducts: (state) => {
        return state.selectedMenuProducts;
    }
}
