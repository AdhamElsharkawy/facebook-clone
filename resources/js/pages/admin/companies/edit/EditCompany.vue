<template>
    <Dialog
        v-model:visible="companyDialog"
        :style="{ width: '450px' }"
        header="editCompany"
        :modal="true"
        class="p-fluid"
    >
        <img
            :src="company.image_path"
            :alt="company.image"
            v-if="company.image"
            width="150"
            class="mt-0 mx-auto mb-5 block shadow-2"
        />
        <div class="field text-center">
            <div class="p-inputgroup">
                <div class="custom-file">
                    <FileUpload
                        mode="basic"
                        accept="image/*"
                        customUpload
                        :maxFileSize="2048000"
                        :chooseLabel="$t('chooseImage')"
                        @change="uploadImage"
                        ref="fileUploader"
                        class="m-0"
                    />
                </div>
            </div>
        </div>

        <div class="field">
            <label
                for="name"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >name</label
            >
            <InputText
                id="name"
                v-model.trim="company.name"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !company.name },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !company.name">
                    nameIsRequired
                </small>
        </div>

        <template #footer>
            <div
                :class="{
                    'flex flex-row-reverse float-left': $store.getters.isRtl,
                }"
            >
                <Button
                    :label="$t('cancel')"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="hideDialog"
                />
                <Button
                    :label="$t('submit')"
                    icon="pi pi-check"
                    class="p-button-text"
                    @click="updateCompany"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import {useToast} from "primevue/usetoast";

export default {
    data() {
        return {
            company: {},
            companyDialog: false,
            submitted: false,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.company.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateCompany() {
            this.submitted = true;
            if ( this.company.name.trim()) {
                this.loading = true;
                const formData = new FormData();

                formData.append("name", this.company.name);
                if( typeof this.company.image =='object'){
                    formData.append("image",this.company.image)
                }

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/companies/" + this.company.id, formData)
                    .then((response) => {
                        this.toast.add({
                            severity: "success",
                            summary: "Successful",
                            detail: response.data.message,
                            life: 3000,
                        });
                        this.hideDialog();
                    })
                    .catch((errors) => {
                        if (errors.response) {
                            this.toast.add({
                                severity: "error",
                                summary: "Error",
                                detail: errors.response.data.message,
                                life: 15000,
                            });
                        }
                    })
                    .then(() => {
                        this.loading = false;
                    });
            }
        }, //end of updateCompany

        editCompany(editCompany) {
            this.company = {...editCompany};
            this.companyDialog = true;
        }, //end of editCompany

        openDialog(company) {
            this.company = company;
            this.companyDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.company = {};
            this.companyDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
