export default {

    setDataList: (state, data) => {
        state.dataList = data;
    },

    setDataDetails: (state, data) => {
        state.dataDetails = data;
        state.typeLimitNumbers = state.typeLimits[state.dataDetails.type];
    },

    setInitialDataDetails: (state) => {
        state.dataDetails = {
            id: 0,
            number: '',
            name: '',
            description: '',
            type: '',
            type_limit: 0,
            remarks: 0,
            is_active: 1,
            is_optional: 0,
            no_discount: 1,
            image: null,
        };
    },

    setTypeData: (state, data) => {
        state.types = data.types;
        state.typeLimits = data.typeLimits;
    },

    setOptionProductsListData: (state, data) => {
        state.dataDetails = {...data};
        state.optionGroupProductsList = data.products;
    },

    setProductsListData: (state, data) => {
        state.productsList = data;
    }

}
