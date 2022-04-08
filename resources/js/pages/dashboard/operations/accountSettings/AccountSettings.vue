<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-row class="flex-grow-0" dense>
            <v-col v-if="isLoading" cols="12" class="text-center">
                <v-progress-circular
                    indeterminate
                    color="primary"
                ></v-progress-circular>
            </v-col>
            <v-col v-else cols="12">
                <v-card>
                    <v-card-title>
                        Account Settings
                        <v-spacer></v-spacer>
                    </v-card-title>
                    <v-tabs vertical >
                        <v-tab class="vertical_tab">
                            Background
                        </v-tab>
                        <v-tab  class="vertical_tab">
                            Colors
                        </v-tab>
                        <v-tab class="vertical_tab">
                            Logo
                        </v-tab>
                        <v-tab class="vertical_tab">
                            Welcome message
                        </v-tab>
                        <v-tab class="vertical_tab">
                            Piggy
                        </v-tab>
                        <v-tab-item class="tab_item">
                            <v-card flat>
                                <v-card-text class="settings_column_parent">
                                    <div class="settings_column_left">
                                        <h1>Background Image</h1>
                                        <div class="dropzone-account-settings">
                                            <vue-dropzone
                                                dropzone-title="Drop your image here or browse"
                                                dropzone-sub-title="Supports: jpg, png"
                                                dropzone-image="/images/image_drag_drop.svg"
                                                @success="form.background_image = $event.file"
                                                @removed="removeBackgroundImage"
                                                :manuallfyAddedFile="form.background_image"
                                            >
                                            </vue-dropzone>
                                        </div>
                                    </div>
                                    <div class="settings_column_right">
                                        <h1>Display Options</h1>
                                        <span class="account_settings_subtitle">Background Color</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.background_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.background_color"
                                            >
                                            <button
                                                @click="form.background_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                            <div style="padding: 16px">
                                <button type="submit" @click="updateSettings" class="default_button settings_save_button">
                                    Save Changes
                                </button>
                            </div>
                        </v-tab-item>
                        <v-tab-item class="tab_item">
                            <v-card flat>
                                <v-card-text class="settings_column_parent">
                                    <div class="settings_column_left">
                                        <h1>Login</h1>
                                        <span class="account_settings_subtitle">Button</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.button_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.button_color"
                                            >
                                            <button
                                                @click="form.button_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                        <span class="account_settings_subtitle">Header</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.tabs_header_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.tabs_header_color"
                                            >
                                            <button
                                                @click="form.tabs_header_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                    </div>
                                    <div class="settings_column_right">
                                        <h1>Header</h1>
                                        <span class="account_settings_subtitle">Text</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.text_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.text_color"
                                            >
                                            <button
                                                @click="form.text_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                        <span class="account_settings_subtitle">Link</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.link_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.link_color"
                                            >
                                            <button
                                                @click="form.link_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                        <span class="account_settings_subtitle">Background</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.header_background_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.header_background_color"
                                            >
                                            <button
                                                @click="form.header_background_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                        <span class="account_settings_subtitle">Language</span>
                                        <div style="display: flex">
                                            <color-picker
                                                class="background_color"
                                                v-model="form.icon_color"
                                                :position="{left: 0, top: '60px'}"
                                                @changeColor="changeColor"
                                            >
                                            </color-picker>
                                            <input
                                                class="color-hex"
                                                readonly
                                                @click="copyText"
                                                :value="form.icon_color"
                                            >
                                            <button
                                                @click="form.icon_color = ''"
                                                class="clear_button">Clear
                                            </button>
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                            <div style="padding: 16px;margin-top: 70px">
                                <button type="submit" @click="updateSettings" class="default_button settings_save_button">
                                    Save Changes
                                </button>
                            </div>
                        </v-tab-item>
                        <v-tab-item class="tab_item">
                            <v-card flat>
                                <v-card-text class="settings_column_parent">
                                    <div class="settings_column_left">
                                        <h1>Logo</h1>
                                        <div class="dropzone-account-settings">
                                            <vue-dropzone
                                                dropzone-title="Drop your image here or browse"
                                                dropzone-sub-title="Supports: jpg, png"
                                                @success="dropdownResponse"
                                                @removed="removedFile"
                                                :manuallfyAddedFile="form.logo"
                                            >
                                            </vue-dropzone>
                                        </div>
                                    </div>
                                    <div class="settings_column_right">
                                        <h1>Display Options</h1>
                                        <span class="account_settings_subtitle">Logo Sizes</span>
                                        <div style="display: block">
                                            <div class="logo_sizes">
                                                <div>Original Width</div>
                                                <input
                                                    type="number"
                                                    readonly
                                                    disabled
                                                    v-model="form.logo_original_width"
                                                >px
                                            </div>
                                            <div class="logo_sizes">
                                                <div>Original Height</div>
                                                <input
                                                    type="number"
                                                    readonly
                                                    disabled
                                                    v-model="form.logo_original_height"
                                                >px
                                            </div>
                                        </div>
                                        <div style="display: block">
                                            <div class="logo_sizes">
                                                <div>Width</div>
                                                <input
                                                    type="number"
                                                    v-model="form.logo_width"
                                                >px
                                            </div>
                                            <div class="logo_sizes">
                                                <div>Height</div>
                                                <input
                                                    type="number"
                                                    v-model="form.logo_height"
                                                >px
                                            </div>
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                            <div style="padding: 16px">
                                <button type="submit" @click="updateSettings" class="default_button settings_save_button">
                                    Save Changes
                                </button>
                            </div>
                        </v-tab-item>
                        <v-tab-item class="tab_item">
                            <v-card flat>
                                <v-card-text class="settings_column_parent">
                                    <div class="settings_column_left">
                                        <h1>Welcome Message</h1>
                                        <div class="ckeditor-account-settings">
                                            <ckeditor
                                                :editor="editor"
                                                v-model="form.editor_data"
                                                :config="editorConfig"
                                            >
                                            </ckeditor>
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                            <div style="padding: 16px">
                                <button type="submit" @click="updateSettings" class="default_button settings_save_button">
                                    Save Changes
                                </button>
                            </div>
                        </v-tab-item>
                        <v-tab-item class="tab_item">
                            <v-card flat>
                                <v-card-text class="settings_column_parent">
                                    <div class="settings_column_left">
                                        <div class="piggy-account-settings piggy-account-settings-checkbox">
                                            <label for="piggy" class="account_settings_row">Activate Piggy</label>
                                            <input
                                                type="checkbox"
                                                id="piggy"
                                                true-value="yes"
                                                false-value="no"
                                                v-model="form.piggy_checkbox"
                                            >
                                        </div>
                                        <div class="piggy-account-settings">
                                            <label for="secret_token" class="account_settings_row">Secret Token</label>
                                            <input
                                                type="text"
                                                id="secret_token"
                                                v-model="form.piggy_secret_token"
                                            >
                                        </div>
                                        <div class="piggy-account-settings">
                                            <label for="client_id" class="account_settings_row">Client Id</label>
                                            <input
                                                type="number"
                                                id="client_id"
                                                v-model="form.piggy_client_id"
                                            >
                                        </div>
                                        <div class="piggy-account-settings">
                                            <label for="shop_id" class="account_settings_row">Shop Id</label>
                                            <input
                                                type="number"
                                                id="shop_id"
                                                v-model="form.piggy_shop_id"
                                            >
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                            <div style="padding: 16px">
                                <button type="submit" @click="updateSettings" class="default_button settings_save_button">
                                    Save Changes
                                </button>
                            </div>
                        </v-tab-item>
                    </v-tabs>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import vueDropzone from "../../../../components/dropzone/vueDropzone";
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import CKEditor from '@ckeditor/ckeditor5-vue2';
    import UploadAdapter from '../../../../UploadAdapter';
    import {mapActions, mapState} from 'vuex';

    export default {
        name: "AccountSettings",
        components: {vueDropzone, ckeditor: CKEditor.component},
        data() {
            return {
                editor: ClassicEditor,
                editorConfig: {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'insertTable', '|', 'imageUpload', 'mediaEmbed', '|', 'undo', 'redo' ],
                    table: {
                        toolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
                    },
                    extraPlugins: [this.uploader],
                    language: 'nl',
                }
            };
        },
        computed:{
            ...mapState({
                form:(state) => state.settings.form,
                isLoading:(state) => state.settings.isSettingsLoading
            })
        },
        methods: {

            ...mapActions({
                createOrUpdateSettings:'updateSettings',
                showSuccess : 'app/showSuccess',
                showError : 'app/showError',
                showToast: 'app/showToast'
            }),

            changeColor(color) {
                const {rgba: {r, g, b, a}} = color
                this.color = `rgba(${r, g, b, a})`
            },
            copyText(event){
                /* Select the text field */
                event.target.select();
                event.target.setSelectionRange(0, 99999); /* For mobile platforms */

                /* Copy the text inside the text field */
                navigator.clipboard.writeText(event.target.value);
                this.showToast('Copied!')

            },
            updateSettings: function () {
                this.createOrUpdateSettings(this.form).then(response => {
                    this.showSuccess(response.message);
                }).catch(error =>{
                    this.showError({ message: "Error : ", error: error });
                })
            },
            dropdownResponse: function (event) {
                this.form.logo = event.file
                this.form.logo_original_width = event.file.width
                this.form.logo_original_height = event.file.height
            },
            removedFile: function (event){
                this.form.logo = null
                this.form.logo_original_width = ''
                this.form.logo_original_height = ''
                this.form.logo_width = ''
                this.form.logo_height = ''
            },
            removeBackgroundImage: function (event){
                this.form.background_image = null
            },
            uploader(editor)
            {
                editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
                    return new UploadAdapter( loader );
                };
            },
            // isChecked(){
            //     if(this.form.piggy_checkbox === 'true'){
            //         return 'true'
            //     }else{
            //         return 'false'
            //     }
            // },
        },
        mounted() {
            this.$store.dispatch('fetchSettings');
        },
    }
</script>

<style scoped>
.vertical_tab{
    justify-content: left;
    border-right: 1px solid #1E1E1E0D;
    border-bottom: 1px solid #1E1E1E0D;
    border-top: 1px solid #1E1E1E0D;
    background: #FFFFFF 0 0 no-repeat padding-box;
    border-radius: 0 35px 35px 0;
    opacity: 1;
    margin-bottom: 15px;
    width: 300px;
    height: 70px !important;
    padding: 23px 0 23px 35px;
}
.settings_column_left{
    width: 50%;
}
.settings_column_right{
    width: 50%;
}
.settings_column_right h1, .settings_column_left h1{
    font: normal normal 600 20px/25px Quicksand;
    text-align: left;
    letter-spacing: 0;
    margin-bottom: 30px;
    color: #212121;
    opacity: 1;
}
.settings_column_parent{
    display: flex ;
}

@media( max-width:1450px ){
    .settings_column_parent{
        display: block !important;
    }
    .settings_column_right{
        margin-top: 40px;
    }
}
.color-hex{
    cursor: pointer;
}

.logo_sizes{
    margin-bottom: 15px;
    display: flex;
    text-align: left;
    font: normal normal 16px/20px Quicksand;
    letter-spacing: 0;
    color: #000000;
    opacity: 0.85;
    margin-top: 25px;
}
.logo_sizes input{
    margin: 0 5px 0 5px;
    padding: 5px;
    border: 1px solid gray;
}
.logo_sizes div{
    min-width: 125px;
    width: 125px;
}
.ckeditor-account-settings{
    width: 100%;
}
</style>
