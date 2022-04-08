import actions from './actions';
import getters from './getters';
import mutations from './mutations';

const state = {
    dataDetails: {
        id: 0,
        name: "",
        description: "",
        is_blocked: false,
        start_date: null,
        end_date: null,
    },
    categoryList: '',
    productList: '',
    selectedCategory: [],
    selectedSourceShop: [],
    selectedMenuProductIds:[],
    selectedMenuProducts:[]
}

export default {
    state,
    actions,
    getters,
    mutations,
    namespaced: true
}
