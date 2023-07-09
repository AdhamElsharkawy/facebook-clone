<template>
    <Dialog
        v-model:visible="userDialog"
        :style="{ width: '450px' }"
        :header="$t('editUser')"
        :modal="true"
        class="p-fluid"
    >
        <img
            :src="user.image_path"
            :alt="user.image"
            v-if="user.image"
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
                for="title"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >title</label
            >
            <InputText
                id="title"
                v-model.trim="user.title"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !user.title },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.title">
                    titleIsRequired
                </small>
        </div>

        <div class="field">
            <label
                for="name"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >name</label
            >
            <InputText
                id="name"
                v-model.trim="user.name"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !user.name },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.name">
                    nameIsRequired
                </small>
        </div>

        <div class="field">
            <label
                for="email"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >email</label
            >
            <InputText
                id="email"
                v-model.trim="user.email"
                required="true"
                type="email"
                :class="[
                    { 'p-invalid': submitted && !user.email },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.email">
                    emailIsRequired
                </small>
        </div>

        <div class="field">
            <label
                for="mobile"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >mobile</label
            >
            <InputText
                id="mobile"
                v-model.trim="user.mobile"
                required="true"
                type="mobile"
                :class="[
                    { 'p-invalid': submitted && !user.mobile },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.mobile">
                    mobileIsRequired
                </small>
        </div>

        <div class="field">
            <label
                class="mb-3"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >role</label
            >
            <div class="formgrid grid">
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="role1"
                        name="role"
                        value="admin"
                        v-model="user.role"
                    />
                    <label for="role1">admin</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="role2"
                        name="role"
                        value="user"
                        v-model="user.role"
                    />
                    <label for="role2">user</label>
                </div>
            </div>
        </div>
        <div class="field">
            <label
                class="mb-3"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >status</label
            >
            <div class="formgrid grid">
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="status1"
                        name="status"
                        value="active"
                        v-model="user.status"
                    />
                    <label for="status1">Active</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="status2"
                        name="status"
                        value="blocked"
                        v-model="user.status"
                    />
                    <label for="status2">blocked</label>
                </div>
            </div>
        </div>

        <div class="field">
            <label
                for="birth_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >birth_date</label
            >
            <Calendar required="true" id="birth_date" v-model="user.birth_date" :class="[{ 'p-invalid': submitted && !user.birth_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !user.birth_date">
                    birth_dateIsRequired
                </small>
        </div>

        <div class="field">
            <label
                for="score"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >score</label
            >
            <InputText
                id="score"
                v-model.number="user.score"
                required="true"
                type="number"
                :class="[
                    { 'p-invalid': submitted && !user.score },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.score">
                    scoreIsRequired
                </small>
        </div>

        <div class="field">
            <label
                for="departments"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Department</label
            >
            <Dropdown v-model="selectedOption" :options="departments" optionLabel="name"
                      placeholder="Select a Department" class="w-full md:w-14rem"/>
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
                    @click="updateUser"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import {useToast} from "primevue/usetoast";

export default {
    props: ["departments"],
    data() {
        return {
            user: {},
            userDialog: false,
            submitted: false,
            selectedOption: null,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.user.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateUser() {
            this.submitted = true;

            if (this.user.name && this.user.name.trim() && this.user.email) {
                this.loading = true;
                const formData = new FormData();
                const options = {
                    year: "numeric",
                    month: "2-digit",
                    day: "numeric"
                };
                // if condition this.user.birthdate is yyyy-mm-dd
                let regEx = /^\d{4}-\d{2}-\d{2}$/;
                let convertedDate;
                if(this.user.birth_date !== regEx && typeof this.user.birth_date == 'object'){
                    console.log(typeof this.user.birth_date);
                    const formattedDate = this.user.birth_date.toLocaleDateString('en-US', options).replace(/\//g, '-');
                    const [month, day, year] = formattedDate.split('-').map(Number);
                     convertedDate = `${year}-${month}-${day}`;
                    console.log(typeof convertedDate);
                    console.log(convertedDate);
                    this.user.birth_date = convertedDate;
                }
                formData.append("title", this.user.title);
                formData.append("name", this.user.name);
                formData.append("email", this.user.email);
                formData.append("mobile", this.user.mobile);
                formData.append("status", this.user.status);
                formData.append("score", this.user.score);
                formData.append("role", this.user.role);
                if( typeof this.user.image =='object'){
                    formData.append("image",this.user.image)
                }
                // this.user.department.name = this.selectedOption.name;
                formData.append("department_id", this.selectedOption.id);
                formData.append("birth_date", convertedDate ?? this.user.birth_date);
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/users/" + this.user.id, formData)
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
        }, //end of updateUser

        editUser(editUser) {
            this.user = {...editUser};
            this.userDialog = true;
        }, //end of editUser

        openDialog(user) {
            this.user = user;
            this.userDialog = true;
            this.selectedOption = this.user.department;
        }, //end of openDialog

        hideDialog() {
            this.user = {};
            this.userDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
