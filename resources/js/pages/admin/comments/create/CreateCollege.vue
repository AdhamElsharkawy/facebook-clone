<template>
    <Loading v-if="loading" />
    <Dialog
        v-model:visible="newCollegeDialog"
        :style="{ width: '450px' }"
        header="newCollege"
        :modal="true"
        class="p-fluid"
    >
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
                >{{ $t("name") }}</label
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
            <small class="p-invalid" v-if="submitted && !college.name">{{
                $t("nameIsRequired")
            }}</small>
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
                    @click="createCollege"
                />
            </div>
        </template>
    </Dialog>
</template>
<script>
import { useToast } from "primevue/usetoast";

export default {
    emits: ["collegeCreated"],
    data() {
        return {
            newCollegeDialog: false,
            selectedOption: null,
            college: {
                name: "",
                image: "",
            },
            submitted: false,
            loading: false,
        };
    }, //end of data

    beforeMount() {
        this.toast = useToast();
    },

    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.college.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        createCollege() {

            this.submitted = true;
            if (
                this.college.name.trim()
            ) {
                this.loading = true;
                const formData = new FormData();
                for (let key in this.college) {
                    formData.append(key, this.college[key]);
                }
                axios
                    .post("/api/admin/colleges", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        this.toast.add({
                            severity: "success",
                            summary: "Successful",
                            detail: response.data.message,
                            life: 3000,
                        });
                        this.$emit("collegeCreated");
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
        }, //end of createCollege

        openDialog() {
            this.newCollegeDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.college = {};
            this.submitted = false;
            this.newCollegeDialog = false;
        }, //end of hideDialog
    },
};
</script>
