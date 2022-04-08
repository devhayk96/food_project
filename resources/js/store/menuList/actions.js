import axios from "axios";

const fetchMenuList = async ({
    commit
}) => {
    await axios
        .get("/web/v1/menu")
        .then(function (response) {
            commit('setMenuList', response.data.data)
        })
        .catch(function (resp) {
            console.log(resp);
        });
}

const setMenuListItemStatus = async ({
        dispatch
    },
    item
) => {
    await axios.post(
        '/web/v1/menu/update-status', {
            menu: item
        }
    ).then(function (response) {
        if (response.data.success) {
            dispatch('fetchMenuList');
            dispatch('app/showSuccess', 'Menu Status Successfully updated', {
                root: true
            });
        }
    }).catch(function (e) {
        console.log(e)
    })
}

const deleteMenuListItem = async ({
        dispatch
    },
    id
) => {
    await axios.post('/web/v1/menu/menu-delete', {
        id: id
    }).then(function (response) {
        if (response.data.success) {
            dispatch('fetchMenuList');
            dispatch('app/showSuccess', response.data.message, {
                root: true
            });
        }
    }).catch(function (e) {
        console.log(e)
    })
}

export default {
    fetchMenuList,
    deleteMenuListItem,
    setMenuListItemStatus
}
