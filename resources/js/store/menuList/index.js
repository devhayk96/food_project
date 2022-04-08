import getters from './getters'
import actions from './actions';
import mutations from './mutations';

const state = {
    menuList: []
}

export default {
    state,
    actions,
    getters,
    mutations,
    namespaced: true
}
