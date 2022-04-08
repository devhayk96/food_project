import axios from 'axios';

const fetchCustomers = async ({
    commit
}) => {
    await axios.get('/web/v1/customer/fetch-all')
        .then(response => {
            if(response.status === 200) {
                commit('setCustomersData', response.data.customers_data)
            }
        }).catch(error => {
            console.log(error)
        })
}

export default {
    fetchCustomers
}
