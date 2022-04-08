const fetchOptionGroupList = async ({commit}) => {
    axios.get('/web/v1/optional-group')
        .then( ({data}) => {
            commit('setDataList', data.data);
        })
        .catch( error => {
            console.log(error);
        });
}

const fetchOptionGroupDetails = async ({state, commit}) => {
    axios.get(`/web/v1/optional-group/${state.dataDetails.id}`)
        .then( ({data}) => {
            commit('setDataDetails', data);
        })
        .catch( error => {
            console.log(error);
        });
}

const getOptionalGroupTypes = async ({commit}) => {
    axios.get(`/web/v1/optional-group-types`)
        .then( ({data}) => {
            commit('setTypeData', data);
        })
        .catch( error => {
            console.log(error);
        });
}

const fetchOptionGroupWithProducts = async ({state, commit}) => {
    axios.get(`/web/v1/optional-group-product/${state.dataDetails.id}`)
        .then( ({data}) => {
            commit('setOptionProductsListData', data);
        })
        .catch( error => {
            console.log(error);
        });
}

const saveProductsInOptionGroup = async ({state, commit, dispatch}, data) => {

    axios.post(`/web/v1/optional-group-product/${state.dataDetails.id}`,data)
        .then( ({data}) => {
            dispatch('fetchOptionGroupWithProducts');
        })
        .catch( error => {
            console.log(error);
        });
}

const fetchProductsList = async ({state, commit}, filter) => {
    let queryString = '';
    Object.keys(filter).map(key => {
        if(filter[key])  {
            queryString += `${key}=${filter[key]}&`
        }
    });

    axios.get(`/web/v1/simple/product?${queryString}`)
        .then( ({data}) => {
            commit('setProductsListData', data);
        })
        .catch( error => {
            console.log(error);
        });
}


export default {
    fetchOptionGroupList,
    fetchOptionGroupDetails,
    getOptionalGroupTypes,
    fetchOptionGroupWithProducts,
    fetchProductsList,
    saveProductsInOptionGroup
}
