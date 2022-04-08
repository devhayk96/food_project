import actions from './actions'
import mutations from './mutations'

// state initial values
const state = {
    shopsList: [],

}

export default {
    namespaced: true,
    state,
    actions,
    mutations
}
