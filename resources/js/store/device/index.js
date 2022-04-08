import actions from './actions'
import mutations from './mutations'

// state initial values
const state = {
    listData: [],

    dataDetails: {
        id: 0,
        name: null,
        code: null,
        shop_id: null,
        orders_auto_refresh_time: null
    },

}

export default {
    namespaced: true,
    state,
    actions,
    mutations
}
