import actions from './actions'
import mutations from './mutations'

// state initial values
const state = {
    dataList: [],
    productsList: [],
    optionGroupProductsList: [],
    selectedProductIds: [],

    searchProductsQuery: {
        per_page: 25,
        id: null,
        name: null,
        date: null,
        sortBy: null,
        sortByType: null
    },

    dataDetails: {
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
    },
    typeLimitNumbers: [],
    typeLimits: {},
    types: [],
}

export default {
    namespaced: true,
    state,
    actions,
    mutations
}
