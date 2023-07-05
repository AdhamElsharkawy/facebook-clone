<template>
    <Toast />
    <div class="col-12">
        <div class="card">
            <h5 class="mb-5">{{ $t("Seos") }}</h5>
            <Loading v-if="loading" />
            <div v-else class="p-fluid formgrid grid">
                <div class="field col-12 ">
                    <span class="p-float-label">
                        <InputText
                            type="text"
                            id="title"
                            v-model="title"
                            required="true"
                            :placeholder="$t('english')"
                            :class="{ 'p-invalid': submitted && !title }"
                        />
                        <label for="title">{{ $t("title") }}</label>
                    </span>
                    <small class="p-invalid" v-if="submitted && !title">{{
                        $t("titleInEnglishIsRequired")
                    }}</small>
                </div>

                <div class="field col-12 ">
                    <span class="">
                        <label for="description">{{
                            $t("description")
                        }}</label>
                    </span>
                    <span class="p-float-label">
                        <Editor
                            id="description"
                            v-model="description"
                            editorStyle="height: 320px"
                            aria-required="true"
                            :modules="$store.getters.getEditorOptions.modules"
                            :class="{
                                'border rounded-lg border-red-500':
                                    submitted && !description,
                            }"
                            :placeholder="$t('english')"
                        />
                        <small
                            class="p-invalid"
                            v-if="submitted && !description"
                            >{{ $t("descriptionInEnglishIsRequired") }}</small
                        >
                    </span>
                </div>

                <div class="field col-12 mt-4">
                    <span class="p-float-label">
                        <Chips
                            inputId="chips"
                            v-model="keywords"
                            :class="[
                                {
                                    'p-invalid':
                                        submitted && keywords.length === 0,
                                },
                            ]"
                        ></Chips>
                        <label for="chips">Chips</label>
                    </span>
                    <small
                        class="p-invalid"
                        v-if="submitted && keywords.length === 0"
                        >{{ $t("keywordsAreRequired") }}</small
                    >
                </div>

                <div class="field col-12 mt-4">
                    <div
                        class="flex justify-content-between"
                        :class="[{ 'flex-row-reverse': $store.getters.isRtl }]"
                    >
                        <div>
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
                        <div>
                            <Button
                                icon="pi pi-check"
                                :label="$t('submit')"
                                class="p-mt-2 m-0"
                                @click.prevent="updateSeo"
                                :disabled="loading"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useToast } from "primevue/usetoast";

export default {
    data() {
        return {
            title: "",
            description: "",
            keywords: [],
            image: null,
            loading: false,
            submitted: false,
        };
    }, // end of data

    methods: {
        fill() {
            this.loading = true;
            axios
                .get("/api/admin/seos")
                .then((response) => {
                    this.title = response.data.seo.title;
                    this.description = response.data.seo.description;
                    this.keywords = response.data.seo.keywords.split(",");
                })
                .catch((error) => {
                    if (error.response) {
                        this.toast.add({
                            severity: "error",
                            summary: "Error",
                            detail: error.response.data.message,
                            life: 15000,
                        });
                    }
                })
                .then(() => {
                    this.loading = false;
                });
        }, // end of fill
        formatKeywords() {
            let stringedKeywords = "";
            this.keywords.forEach((keyword) => {
                stringedKeywords += keyword + ",";
            });
            stringedKeywords = stringedKeywords.slice(0, -1);
            return stringedKeywords;
        }, // end of formatKeywords

        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.image = this.$refs.fileUploader.files[0];
        }, // end of onUpload

        updateSeo() {
            this.submitted = true;
            if (
                this.title &&
                this.description &&
                this.keywords.length > 0
            ) {
                this.loading = true;
                const formData = new FormData();
                formData.append("title", JSON.stringify(this.title));
                formData.append(
                    "description",
                    JSON.stringify(this.description)
                );
                formData.append("keywords", this.formatKeywords());
                if (this.image) formData.append("image", this.image);
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/seos/1", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        this.toast.add({
                            severity: "success",
                            summary: "Success",
                            detail: response.data.message,
                            life: 3000,
                        });
                    })
                    .catch((error) => {
                        if (error.response) {
                            this.toast.add({
                                severity: "error",
                                summary: "Error",
                                detail: error.response.data.message,
                                life: 15000,
                            });
                        }
                    })
                    .then(() => {
                        this.loading = false;
                        this.submitted = false;
                    });
            }
        }, // end of updateSeo
    }, // end of methods

    beforeMount() {
        this.toast = useToast();
    }, // end of beforeMount

    mounted() {
        this.fill();
    }, // end of mounted
};
</script>

<style lang="scss">
.right-to-left {
    .p-editor-content {
        .ql-editor {
            text-align: right;
        }
    }
}
</style>
