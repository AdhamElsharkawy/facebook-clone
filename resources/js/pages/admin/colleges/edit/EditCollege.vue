<template>
    <Dialog
        v-model:visible="collegeDialog"
        :style="{ width: '450px' }"
        header="editCollege"
        :modal="true"
        class="p-fluid"
    >
        <img
            :src="college.image_path"
            :alt="college.image"
            v-if="college.image"
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
                v-model.trim="college.name"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !college.name },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !college.name">
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
                    @click="updateCollege"
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
            college: {},
            collegeDialog: false,
            submitted: false,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.college.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateCollege() {
            this.submitted = true;
            if ( this.college.name.trim()) {
                this.loading = true;
                const formData = new FormData();

                formData.append("name", this.college.name);
                if( typeof this.college.image =='object'){
                    formData.append("image",this.college.image)
                }

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/colleges/" + this.college.id, formData)
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
        }, //end of updateCollege

        editCollege(editCollege) {
            this.college = {...editCollege};
            this.collegeDialog = true;
        }, //end of editCollege

        openDialog(college) {
            this.college = college;
            this.collegeDialog = true;
            // this.selectedOption = this.college.department;
        }, //end of openDialog

        hideDialog() {
            this.college = {};
            this.collegeDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
