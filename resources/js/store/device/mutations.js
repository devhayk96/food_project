export default {

    setDataList: (state, data) => {
        state.listData = data.data;
    },

    setDataDetails: (state, data) => {
        state.dataDetails = data.data;
    },

    setInitialDataDetails: (state) => {
        state.dataDetails = {
            id: 0,
            name: null,
            code: null,
            shop_id: null,
            orders_auto_refresh_time: null
        };
    },

}
