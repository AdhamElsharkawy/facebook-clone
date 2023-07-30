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
                                @click="createNewCompany"
                            />
                            <Button
                                :label="$t('delete')"
                                icon="pi pi-trash"
                                class="p-button-danger"
                                @click="confirmDeleteSelected"
                                :disabled="
                                    !selectedCompanies || !selectedCompanies.length
                                "
                            />
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button
                            :label="$t('export')"
                            icon="pi pi-upload"
                            class="p-button-help"
                            @click="exportCSV($company)"
                        />
                    </template>
                </Toolbar>

                <company-list
                    ref="listCompanyComponent"
                    :currentCompanies="currentCompanies"
                    :totalPages="totalPages"
                    :currentPage="currentPage"
                    @pageChange="pageChange"
                    @selectCompanies="selectCompanies"
                    @editCompany="editCompany"
                    @deleteCompany="fill"
                ></company-list>

                <edit-company ref="editCompanyComponent"  ></edit-company>

                <create-company
                    ref="createCompanyComponent"
                    @companyCreated="fill"
                ></create-company>

                <Dialog
                    v-model:visible="deleteCompaniesDialog"
                    :style="{ width: '450px' }"
                    header="Confirm"
                    :modal="true"
                >
                    <div class="flex align-items-center justify-content-center">
                        <i
                            class="pi pi-exclamation-triangle mr-3"
                            style="font-size: 2rem"
                        />
                        <span v-if="company"
                            >Are you sure you want to delete the selected
                            companies?</span
                        >
                    </div>
                    <template #footer>
                        <Button
                            label="No"
                            icon="pi pi-times"
                            class="p-button-text"
                            @click="deleteCompaniesDialog = false"
                        />
                        <Button
                            label="Yes"
                            icon="pi pi-check"
                            class="p-button-text"
                            @click="deleteSelectedCompanies"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>

<script>
import CompanyList from "./CompanyList.vue";
import EditCompany from "../edit/EditCompany.vue";
import CreateCompany from "../create/CreateCompany.vue";
import { useToast } from "primevue/usetoast";

export default {
    components: { CompanyList, EditCompany, CreateCompany },
    data() {
        return {
            currentCompanies: [],
            deleteCompaniesDialog: false,
            selectedCompanies: null,
            loading: false,
            isEmpty: false,
            errors: null,
            totalPages: 0,
            currentPage: 1,
        };
    }, //end of data

    methods: {
        createNewCompany() {
            this.company = {};
            this.$refs.createCompanyComponent.openDialog();
        }, //end of openNew

        deleteSelectedCompanies() {
            this.loading = true;
            axios
                .delete("/api/admin/companies/delete/all", {
                    data: {
                        companies: this.selectedCompanies.map((val) => val.id),
                    },
                })
                .then((response) => {
                    this.currentCompanies = this.currentCompanies.filter(
                        (val) => !this.selectedCompanies.includes(val)
                    );
                    this.selectedCompanies = null;
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
                    this.deleteCompaniesDialog = false;
                });
        }, //end of deleteSelectedCompanies

        confirmDeleteSelected() {
            this.deleteCompaniesDialog = true;
        }, //end of confirmDeleteSelected

        exportCSV() {
            this.$refs.listCompanyComponent.exportCSV();
        }, //end of exportCSV

        fill(currentPage) {
            this.loading = true;
            axios
                .get(`/api/admin/companies?page=${currentPage}`)
                .then((response) => {
                    console.log(response.data);
                    this.currentCompanies = response.data.companies.data;
                    this.totalPages = response.data.companies.last_page;
                    this.currentPage = response.data.companies.current_page;

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

        selectCompanies(selectedCompanies) {
            this.selectedCompanies = selectedCompanies;
        }, //end of selectCompanies

        editCompany(company) {
            this.$refs.editCompanyComponent.openDialog(company);
        }, //end of editCompany
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount

    created() {
        this.fill();
    }, //end of created
};
</script>
