<template>
    <Loading v-if="loading" />
    <Dialog
        v-model:visible="newDepartmentDialog"
        :style="{ width: '450px' }"
        header="newDepartment"
        :modal="true"
        class="p-fluid"
    >

        <div class="field">
            <label
                for="name"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >name</label
            >
            <InputText
                id="name"
                v-model.trim="department.name"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !department.name },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !department.name">
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
                    @click="createDepartment"
                />
            </div>
        </template>
    </Dialog>
</template>
<script>
import { useToast } from "primevue/usetoast";

export default {
    emits: ["departmentCreated"],

    data() {
        return {
            newDepartmentDialog: false,
            department: {
                name: "",
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
            this.department.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        createDepartment() {
            this.submitted = true;
            if (
                this.department.name &&
                this.department.name.trim()
            ) {
                this.loading = true;
                const formData = new FormData();
                for (let key in this.department) {
                    formData.append(key, this.department[key]);
                }
                axios
                    .post("/api/admin/departments", formData, {
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
                        this.$emit("departmentCreated");
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
        }, //end of createDepartment

        openDialog() {
            this.newDepartmentDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.department = {};
            this.submitted = false;
            this.newDepartmentDialog = false;
        }, //end of hideDialog
    },
};
</script>
