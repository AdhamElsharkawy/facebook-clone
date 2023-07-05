<template>
    <Dialog
        v-model:visible="departmentDialog"
        :style="{ width: '450px' }"
        header="editDepartment"
        :modal="true"
        class="p-fluid"
    >

        <div class="field">
            <label
                for="name"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >{{ $t("name") }}</label
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
            <small class="p-invalid" v-if="submitted && !department.name">{{
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
                    @click="updateDepartment"
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
            department: {},
            departmentDialog: false,
            submitted: false,
            selectedOption: null,
        };
    },
    methods: {
        updateDepartment() {
            this.submitted = true;

            if (this.department.name && this.department.name.trim()) {
                this.loading = true;
                const formData = new FormData();
                formData.append("name", this.department.name);
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/departments/" + this.department.id, formData)
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
        }, //end of updateDepartment

        editDepartment(editDepartment) {
            this.department = {...editDepartment};
            this.departmentDialog = true;
        }, //end of editDepartment

        openDialog(department) {
            this.department = department;
            this.departmentDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.department = {};
            this.departmentDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
