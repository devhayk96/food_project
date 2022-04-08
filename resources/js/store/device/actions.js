const fetchList = async ({state, commit}) => {
    axios.get(`/web/v1/device`)
        .then( ({data}) => {
            commit('setDataList', data);
        })
        .catch( error => {
            console.log(error);
        });
}

const fetchDeviceDetails = async ({state, commit}) => {
    axios.get(`/web/v1/device/${state.dataDetails.id}`)
        .then( ({data}) => {
            commit('setDataDetails', data);
        })
        .catch( error => {
            console.log(error);
        });
}


export default {
    fetchList,
    fetchDeviceDetails
}
