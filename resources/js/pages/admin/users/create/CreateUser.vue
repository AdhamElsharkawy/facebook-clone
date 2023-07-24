<template>
    <Loading v-if="loading" />
    <Dialog
        v-model:visible="newUserDialog"
        :style="{ width: '450px' }"
        :header="$t('newUser')"
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
                for="title"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >{{ $t("title") }}</label
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
                >{{ $t("name") }}</label
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
            <small class="p-invalid" v-if="submitted && !user.name">{{
                $t("nameIsRequired")
            }}</small>
        </div>
        <div class="field">
            <label
                for="email"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >{{ $t("email") }}</label
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
            <small class="p-invalid" v-if="submitted && !user.email">{{
                $t("emailIsRequired")
            }}</small>
        </div>
        <div class="field">
            <label
                class="mb-3"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >{{ $t("role") }}</label
            >
            <div class="formgrid grid">
                <div class="field-radiobutton col-3">
                    <RadioButton
                        id="role1"
                        name="role"
                        value="admin"
                        v-model="user.role"
                    />
                    <label for="role1">admin</label>
                </div>
                <div class="field-radiobutton col-3">
                    <RadioButton
                        id="role3"
                        name="role"
                        value="team_leader"
                        v-model="user.role"
                    />
                    <label for="role3">T-leader</label>
                </div>
                <div class="field-radiobutton col-3">
                    <RadioButton
                        id="role4"
                        name="role"
                        value="manager"
                        v-model="user.role"
                    />
                    <label for="role4">Manager</label>
                </div>
                <div class="field-radiobutton col-3">
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
                for="mobile"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >mobile</label
            >
            <InputText
                id="mobile"
                v-model.trim="user.mobile"
                required="true"
                autofocus
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
            > status</label
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
                for="departments"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Department</label
            >
            <Dropdown v-model="selectedOption" :options="departments" optionLabel="name"
                      placeholder="Select a Department" class="w-full md:w-14rem"/>
        </div>

        <!-- <div class="field">
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
        </div> -->


        <div class="field">
            <label
                for="birth_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >birth_date</label
            >
            <InputText
                id="birth_date"
                v-model.trim="user.birth_date"
                required="true"
                type="date"
                :class="[
                    { 'p-invalid': submitted && !user.birth_date },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.birth_date">
                    birth_dateIsRequired
            </small>
        </div>


        <div class="field">
            <label
                for="password"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >password</label
            >
            <InputText
                id="password"
                v-model.trim="user.password"
                required="true"
                type="password"
                :class="[
                    {
                        'p-invalid':
                            (submitted && !user.password) ||
                            (submitted &&
                                user.password_confirmation !== user.password),
                    },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !user.password">
                passwordIsRequired
            </small>
        </div>
        <div class="field">
            <label
                for="password_confirmation"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >passwordConfirmation</label
            >
            <InputText
                id="password_confirmation"
                v-model.trim="user.password_confirmation"
                required="true"
                type="password"
                :class="[
                    {
                        'p-invalid':
                            (submitted && !user.password) ||
                            (submitted &&
                                user.password_confirmation !== user.password),
                    },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small
                class="p-invalid"
                v-if="submitted && !user.password_confirmation"
                >passwordConfirmationIsRequired</small
            >
            <small
                class="p-invalid"
                v-if="submitted && user.password_confirmation !== user.password"
                >passwordConfirmationMustMatchPassword</small
            >
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
                    @click="createUser"
                />
            </div>
        </template>
    </Dialog>
</template>
<script>
import { useToast } from "primevue/usetoast";

export default {
    emits: ["userCreated"],
    props: ["departments"],

    data() {
        return {
            newUserDialog: false,
            selectedOption: null,
            user: {
                title: "",
                name: "",
                email: "",
                role: "",
                // score: 0,
                mobile: "",
                status: "",
                birth_date: "",
                password: "",
                password_confirmation: "",
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
            this.user.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        createUser() {
            this.submitted = true;
            if (
                this.user.name &&
                this.user.name.trim() &&
                this.user.title &&
                this.user.role &&
                // this.user.score &&
                this.user.mobile &&
                this.user.birth_date &&
                this.selectedOption &&
                this.user.email &&
                this.user.password &&
                this.user.password_confirmation
            ) {
                this.loading = true;
                const formData = new FormData();
                for (let key in this.user) {
                    formData.append(key, this.user[key]);
                }
                formData.append("department_id", this.selectedOption.id);
                axios
                    .post("/api/admin/users", formData, {
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
                        this.$emit("userCreated");
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
        }, //end of createUser

        openDialog() {
            this.newUserDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.user = {};
            this.submitted = false;
            this.newUserDialog = false;
        }, //end of hideDialog
    },
};
</script>
