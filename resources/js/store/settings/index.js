import axios from "axios";

const state = {
    form : {
        logo: null,
        background_color : '#ffffff',
        background_image : null,
        button_color : '#00d0ff',
        icon_color : '#00d0ff',
        link_color : '#00d0ff',
        text_color : '#00d0ff',
        tabs_header_color : '#0160b3',
        header_background_color : '#ffffff',
        logo_original_width : '',
        logo_original_height : '',
        logo_width : '',
        logo_height : '',
        editor_data : '',
        piggy_checkbox : '',
        piggy_secret_token : '',
        piggy_client_id : '',
        piggy_shop_id : '',

    },
    isSettingsLoading:false
}

const actions = {
    fetchSettings : function ({commit,state}){
        commit('SET_SETTING_LOADING',true);
        return new Promise((resolve, reject) => {
           axios.get('/api/v1/account-settings').then((response)=>{
               if( response.data.form )
                commit('SET_SETTING_FORM',response.data.form)
               commit('SET_SETTING_LOADING',false);
               resolve(response)
           }).catch((error)=>{
               reject(error.response)
               commit('SET_SETTING_LOADING',false);
           })
        });
    },
    updateSettings : function ({commit},form){
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            for( let field in form ){
                formData.append(field,form[field]);
            }
            axios.post('/api/v1/account-settings',formData).then(response => {
                resolve(response.data)
            }).catch( error => {
                reject(error.response)
            })
        })
    }
}

const mutations = {
    SET_SETTING_LOADING : function (state,loading){
        state.isSettingsLoading = loading;
    },
    SET_SETTING_FORM : function (state,formData){
        state.form = formData
    }
}

const getters = {

}

export default { state,mutations,actions,getters }
