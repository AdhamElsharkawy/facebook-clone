<template>
    <Toast />
    <Loading v-if="loading" />
    <div class="grid" v-else>
        <div class="col-12">
            <div class="card">
                <Toolbar
                    class="mb-4"
                    :class="{
                        'flex flex-row-reverse': $store.getters['isRtl'],
                    }"
                >
                    <template v-slot:start>
                        <div
                            class="my-2"
                            :class="{
                                'flex flex-row-reverse':
                                    $store.getters['isRtl'],
                            }"
                        >
                            <Button
                                :label="$t('new')"
                                icon="pi pi-plus"
                                class="p-button-success"
                                :class="{
                                    'mr-2': !$store.getters['isRtl'],
                                    'ml-2': $store.getters['isRtl'],
                                }"
                                @click="createNewCertification"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedCertifications || !selectedCertifications.length
                                "
                            />
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button
                            :label="$t('export')"
                            icon="pi pi-upload"
                            class="p-button-help"
                            @click="exportCSV($certification)"
                        />
                    </template>
                </Toolbar>

                <certification-list
                    ref="listCertificationComponent"
                    :currentCertifications="currentCertifications"
                    :totalPages="totalPages"
                    :currentPage="currentPage"
                    @pageChange="pageChange"
                    @selectCertifications="selectCertifications"
                    @editCertification="editCertification"
                    @deleteCertification="fill"
                ></certification-list>

                <edit-certification ref="editCertificationComponent" :colleges="colleges" :users="users" ></edit-certification>

                <create-certification
                    ref="createCertificationComponent"
                    @certificationCreated="fill"
                    :colleges="colleges"
                    :users="users"
                ></create-certification>

                <Dialog
                    v-model:visible="deleteCertificationsDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="certification"
                            >Are you sure you want to delete the selected
                            certifications?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteCertificationsDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedCertifications"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import CertificationList from "./CertificationList.vue";
import EditCertification from "../edit/EditCertification.vue";
import CreateCertification from "../create/CreateCertification.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { CertificationList, EditCertification, CreateCertification },
    data() {
        return {
            currentCertifications: [
            ],
            deleteCertificationsDialog: false,
            selectedCertifications: null,
            loading: false,
            isEmpty: false,
            errors: null,
            colleges: [{"id": null, "name": "No College"}],
            users: [],
            totalPages: 0,
            currentPage: 1,
        };
    }, //end of data

    methods: {
        createNewCertification() {
            this.certification = {};
            this.$refs.createCertificationComponent.openDialog();
        }, //end of openNew

        deleteSelectedCertifications() {
            this.loading = true;
            axios
                .delete("/api/admin/certifications/delete/all", {
                    data: {
                        certifications: this.selectedCertifications.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentCertifications = this.currentCertifications.filter(
                        (val) => !this.selectedCertifications.includes(val)
                    );
                    this.selectedCertifications = null;
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
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
                    this.deleteCertificationsDialog = false;
                });
        }, //end of deleteSelectedCertifications

        confirmDeleteSelected() {
            this.deleteCertificationsDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listCertificationComponent.exportCSV();
        }, //end of exportCSV

        fill(currentPage) {
            this.loading = true;
            axios
                .get(`/api/admin/certifications?page=${currentPage}`)
                .then((response) => {
                    this.currentCertifications = response.data.certifications.data;
                    this.totalPages = response.data.certifications.last_page;
                    this.currentPage = response.data.certifications.current_page;
                     this.colleges = [...this.colleges,...response.data.colleges];
                    // this.colleges = response.data.colleges;
                    this.users = response.data.users;
                })
                .catch((errors) => {
                    this.error = errors.response.data;
                })
                .then(() => {
                    this.loading = false;
                }); //end of axios request
        }, //end of fill function
        pageChange(currentPage) {
            this.fill(currentPage);
        }, //end of pageChange

        selectCertifications(selectedCertifications) {
            this.selectedCertifications = selectedCertifications;
        }, //end of selectCertifications

        editCertification(certification) {
            this.$refs.editCertificationComponent.openDialog(certification);
        }, //end of editCertification
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
