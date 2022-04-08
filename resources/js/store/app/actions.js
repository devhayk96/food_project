import router from '../../router'

const showToast = ({
    state,
    commit
}, message) => {
    if (state.toast.show) commit('hideToast')

    setTimeout(() => {
        commit('showToast', {
            color: 'black',
            message,
            timeout: 3000
        })
    })
}

const showError = ({
    state,
    commit
}, {
    message = 'Failed!',
    error
}) => {
    if (state.toast.show) commit('hideToast')

    setTimeout(() => {
        if (typeof error != 'undefined') {
            message += ' ' + error.message;
        }
        commit('showToast', {
            color: 'error',
            message: message,
            timeout: 10000
        })
    })
}

const showSuccess = ({
    state,
    commit
}, message) => {
    if (state.toast.show) commit('hideToast')

    setTimeout(() => {
        commit('showToast', {
            color: 'success',
            message,
            timeout: 2000
        })
    })
}

const showValidationErrors = async ({state, commit, dispatch}, errors) => {
    let errs = [];
    for (let [key, value] of Object.entries(errors)) {
        errs[key] = value[0];
    }

    commit('setErrors', errs);
    dispatch('showError', { message: '', error: {message: "Please check the form below for errors" } });
}

const getUserPinCodePossibleLengths = async ({
    state,
    commit
}) => {
    axios.get('/web/v1/user/pin-code-possible-lengths').then( ({data}) => {
        commit('setPinCodePossibleLengths', data);
    })
};


const signIn = async ({
    state,
    commit,
    dispatch
}, credentials) => {
    commit('setLoading')
    if (state.isLoggedIn === false) {
        // @todo need to check the necessity because sometimes it causes issues creating multiple XSRF-TOKEN cookies
        //axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post(
            '/web/v1/auth/login', {
                email: credentials.email,
                password: credentials.password
            }
        ).then(response => {
            if (response.status === 200) {
                axios.get('/web/v1/user/get/me')
                    .then(function (response) {
                        if (response.status === 200) {
                            commit('setIsLoggedIn');
                            dispatch('showSuccess', 'User successfully logged in');

                            localStorage.setItem('authUser', JSON.stringify(response.data.user));
                            localStorage.setItem('authUserRoles', response.data.roles);
                            if (response.data.roles === 'api-user') {
                                const clear_data = response.data.permissions.filter(a => a !== 'menus-read')
                                localStorage.setItem('authUserPermissions', clear_data);
                            }else {
                                localStorage.setItem('authUserPermissions', response.data.permissions);
                            }
                            setTimeout(() => router.push('/dashboard/orders/new'), 2250);
                        } else {
                            commit('setSignInError');
                            dispatch('showError', {
                                message: 'We couldn\'t get your data. Please log out and try to login again.'
                            });
                        }
                    })
                    .catch(function (error) {
                        commit('setSignInError');
                        dispatch('showError', {
                            message: 'We couldn\'t get your data. Please log out and try to login again.',
                            error: error
                        });
                    });
            } else {
                let errorMessage = {
                    message: 'Error code ' + response.status,
                    error: {
                        message: response.data.toString()
                    }
                };

                commit('setSignInError');
                dispatch('showError', errorMessage);
            }
        }).catch(error => {
            let errorMessage = {
                message: 'Error code: ' + error.response.status,
                error: {
                    message: error.response.data.errors.email
                }
            };

            commit('setSignInError');
            dispatch('showError', errorMessage);
        });
        //})
    }
}

const logout = ({
    state,
    commit,
    dispatch
}) => {
    if (state.isLoggedIn !== false) {
        axios.post(
            '/web/v1/auth/logout'
        ).then(response => {
            if (response.status === 204) {
                commit('setIsLoggedOut');
                localStorage.removeItem('authUser');
                localStorage.removeItem('authUserRoles');
                localStorage.removeItem('authUserPermissions');
                router.push({
                    name: 'auth-signin'
                });
            } else {
                let errorMessage = {
                    message: 'Error code ' + response.status,
                    error: {
                        message: response.data.toString()
                    }
                };
                dispatch('showError', errorMessage);
            }
        }).catch(error => {
            let errorMessage = {
                message: 'Logout issue.',
                error: {
                    message: 'Something went wrong.'
                }
            };
            dispatch('showError', errorMessage);
        });
    }
};

const autoLogin = ({
    commit
}) => {
    commit('setIsLoggedIn');
}

export default {
    showToast,
    showError,
    showSuccess,
    showValidationErrors,
    signIn,
    logout,
    autoLogin,
    getUserPinCodePossibleLengths,
}
