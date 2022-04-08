import actions from './actions';
import getters from './getters';
import mutations from './mutations';

const state = {
    customersList: [],
};

export default {
    state,
    actions,
    getters,
    mutations,
    namespaced:true
}
