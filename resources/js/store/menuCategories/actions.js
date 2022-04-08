import axios from "axios";

const fetchDetails = async ({
    state,
    commit,
    dispatch
}, id) => {
    const menuId = id || state.dataDetails.id;
    const selectedSourceShop = [];
    await axios.get('/web/v1/menu/' + menuId)
        .then(response => {
            commit('setDataDetails', response.data);
            _.map(response.data.menu_source_shops, (s) => {
                if (s.is_blocked === 0)
                    selectedSourceShop.push({
                        shop_id: s.shop_id,
                        source_id: s.source_id
                    });
            });
            const selectedCategory = [];
            commit('setMenuCategories', _.sortBy(state.dataDetails.menu_categories, ['weight']));
            _.map(response.data.menu_categories, (m) => {
                if (m.is_blocked === 0) {
                    selectedCategory[m.weight - 1] = m.menu_category_id;
                }
            });
            commit('setSelectedCategory', selectedCategory);
            dispatch('reAssignMenuProduct');
        })
        .catch(error => {
            console.log(error);
        });
}

const fetchCategoryList = async ({
    commit
}) => {
    await axios.get('/web/v1/menu-category')
        .then(response => {
            console.log(response)
            commit('setCategoryList', response.data.data);
        })
        .catch(error => {
            console.log(error);
        });
}

const setCategoryStatus = async ({
    commit,
    dispatch
}, id) => {
    await axios.post('/web/v1/menu-category/update-status/' + id, {
            id: id
        })
        .then(response => {
            if (response.status)
                dispatch('app/showSuccess', 'Category Status Successfully Updated', {
                    root: true
                })
        }).catch(e => {
            console.log(e)
        })
}

const setCategoryProductStatus = async ({
    dispatch
}, data) => {
    await axios.post('/web/v1/menu/category-product-status', data)
        .then(response => {
            dispatch('app/showSuccess', response.data.message, {
                root: true
            });
            dispatch('fetchDetails');
        }).catch(error => {
            dispatch('app/showError', error, {
                root: true
            })
        })
}

const saveCategory = async ({
    state,
    dispatch
}, selectedCategory) => {
    await axios.post('/web/v1/menu/category/' + state.dataDetails.id, selectedCategory)
        .then(response => {
            console.log(response);
            dispatch('fetchDetails');
            dispatch('app/showSuccess', 'Successfully Updated.', {
                root: true
            });
            // _this.selectedSourceShop = response.data;
        })
        .catch(error => {
            console.log(error);
        });
}

const fetchProductList = ({
    commit
}) => {
    axios
        .get("/web/v1/simple/product")
        .then(response => {
            commit('setProductList', response.data);
        })
        .catch(error => {
            console.log(error);
        });
}

const reAssignMenuProduct = ({
    state,
    commit
}) => {
    const selectedMenuProducts = [];
    const selectedMenuProductIds = [];
    _.map(state.dataDetails.menu_products, (p) => {
        if (p.is_blocked === 0) {
            selectedMenuProductIds.push(p.product.id);
            selectedMenuProducts.push({
                product_id: p.product.id,
                price: p.price,
                categories: _.filter(_.map(state.dataDetails.menu_categories, (mc) => {
                    return _.find(_.filter(mc.products, ['is_blocked', 0]), ['menu_product_id', p.id]) ?
                        mc.id :
                        0
                }), function (o) {
                    return o > 0;
                })
            });
        }
        commit('setSelectedMenuProductIds', selectedMenuProductIds);
        commit('setSelectedMenuProducts', selectedMenuProducts);
    });
}

const saveMenuProduct = async ({
    state,
    dispatch
}, data) => {
    await axios.post('/web/v1/menu/product/' + state.dataDetails.id, data)
        .then(response => {
            dispatch('fetchDetails');
        })
        .catch(error => {
            console.log(error);
        });
}

const addOptionGroups = ({
    dispatch
}, data) => {
    if (data.length == 0) {
        dispatch('app/showError', 'Select Item', {
            root: true
        })
    } else {
        return new Promise((resolve, reject) => {
            axios.post('/web/v1/menu/add-option-groups', data)
                .then(response => {
                    console.log(response)
                    resolve(response);
                    dispatch('app/showSuccess', 'Option Group Successfully Added', {
                        root: true
                    });
                }).catch(error => {
                    reject(error);
                    console.log(error);
                })
        })
    }
}

const saveMenuProductOrder = ({
    state,
    dispatch
}, p) => {
    console.log('saving weight');
    console.log(p);
    axios.post('/web/v1/menu/product-weight', p)
        .then(response => {
            console.log(response);
            dispatch('app/showSuccess', 'Successfully Updated.', {
                root: true
            });
            dispatch('fetchDetails');
            // _this.selectedSourceShop = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
}

const removeCategoryProduct = async ({
    dispatch
}, data) => {
    await axios.post('/web/v1/menu/remove-product', data, {
            root: true
        })
        .then(response => {
            console.log(response)
            dispatch('app/showSuccess', response.data.message, {
                root: true
            });
            dispatch('fetchDetails');
        }).catch(error => {
            dispatch('app/showError', error, {
                root: true
            });
        })
}

const removeProductOptionalGroup = async ({
    dispatch
}, data) => {
    await axios.post('/web/v1/menu/remove-product-optional-group', data)
        .then(response => {
            dispatch('app/showSuccess', response.data.message, {
                root: true
            });
            dispatch('fetchDetails');
        }).catch(error => {
            dispatch('app/showError', error, {
                root: true
            });
        })
}

const editProductPrice = async ({
    dispatch
}, data) => {
    await axios.post('/web/v1/menu/edit-product-price', data)
        .then(response => {
           if(response.status === 200) {
                dispatch('app/showSuccess', response.data.message, {
                    root: true
                })
            }
        }).catch(error => {
            console.log(error)
        })
}

export default {
    saveCategory,
    fetchDetails,
    addOptionGroups,
    saveMenuProduct,
    fetchProductList,
    editProductPrice,
    fetchCategoryList,
    setCategoryStatus,
    reAssignMenuProduct,
    saveMenuProductOrder,
    removeCategoryProduct,
    setCategoryProductStatus,
    removeProductOptionalGroup,
}
