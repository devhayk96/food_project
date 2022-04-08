export default {
    setCategoryList: (state, data) => {
        state.categoryList = data
    },

    setDataDetails: (state, data) => {
        state.dataDetails = data;
    },

    setSelectedSourceShop: (state, data) => {
        state.selectedSourceShop = data;
    },

    setMenuCategories: (state, data) => {
        state.dataDetails.menu_categories = data;
    },

    setSelectedCategory: (state, data) => {
        state.selectedCategory = data;
    },

    setProductList: (state, data) => {
        state.productList = data;
    },

    setSelectedMenuProductIds: (state, data) => {
        state.selectedMenuProductIds = data;
    },

    setSelectedMenuProducts: (state, data) => {
        state.selectedMenuProducts = data
    }
}
