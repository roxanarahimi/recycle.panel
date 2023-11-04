<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <h3 class="mb-5">ویرایش اسلاید</h3>

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div v-if="isDefined" class="card">
                        <div class="card-body">
                            <div class="w-100 mb-3">
                                <form id="editForm">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <label for="type" class="mb-2">نوع اسلاید</label>
                                            <select id="type" class="form-select" @change="typeChanged">
                                                <option value="image" :selected="data.type == 'image'">تصویر</option>
                                                <option value="video" :selected="data.type == 'video'">ویدئو</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label v-if="type == 'image'" class="form-label">تصویر</label><br/>
                                            <label v-if="type == 'video'"  class="form-label">کاور ویدئو</label><br/>
                                            <image-cropper name="" :src="data.image" caption=""
                                                           :hasCaption="hasCaption" :isRequired="imgRequired"
                                                           :aspect="aspect"/>
                                            <div id="imageHelp" class="form-text error"></div>
                                        </div>
                                        <div v-if="type == 'video'" class="col-md-6 col-lg-3 mb-3">
                                            <label for="video" class="mb-2">شناسه cloudinary ویدئو</label>
                                            <input type="text" id="video" :value="data.video" class="form-control" required>
                                            <div id="videoHelp" class="form-text error"></div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <button class="btn btn-primary" @click.prevent="updateInfo"
                                                    type="submit">
                                                ثبت
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>

    </transition>


</template>

<script>
import ImageCropper from '../../components/ImageCropper';
import btnSubmit from "../../components/BtnSubmit";

export default {
    components: {ImageCropper, btnSubmit},
    data() {
        return {
            id: this.$route.params.id,
            data: {},
            categories: [],
            errors: [],
            image_codes: [],
            image_names: [],
            imgRequired: false,
            hasCaption: false,
            aspect: 1920 / 1080,
            isDefined: false,
            enableClick: true,
            features: [],
            progress: 0,
            type: 'image',
        }
    },

    mounted() {
        this.loadData();
    },

    methods: {
        async loadData() {
            await axios.get('/api/panel/slide/' + this.id)
                .then((response) => {
                    this.type = response.data.type;
                    this.data = response.data;
                })
                .then(() => {
                    this.isDefined = true;
                })
                .catch();
        },
        async updateInfo() {
            this.errors = [];
            let emptyFieldsCount = 0;
            let req = document.querySelectorAll('[required]');
            req.forEach((element) => {
                if (element.value === "") {
                    element.classList.add('hasError');
                    element.nextSibling.innerHTML = "فیلد اجباری";
                    emptyFieldsCount++;
                } else {
                    element.classList.remove('hasError');
                    element.nextSibling.innerHTML = "";
                }
            });

            if (emptyFieldsCount === 0) {

                let data;
                if (this.type == 'image') {
                    data = {
                        type: this.type,
                        image: document.getElementById('Image__code').value,
                        video: null,

                    }
                } else if (this.type == 'video') {
                    data = {
                        type: this.type,
                        image: document.getElementById('Image__code').value,
                        video: document.getElementById('video').value,
                    }
                }
                await axios.post('/api/panel/slide/' + this.$route.params.id, data)
                    .then((response) => {
                        if (response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/slides'});
                            }, 1000);
                        }
                    })
                    .catch((error) => {
                        if (error.response.status === 422) {
                            let errorList = Array(error.response.data);
                            for (var i = 0; i < errorList.length; i++) {
                                // console.log('i', errorList[i]);
                                this.errors = errorList[i];
                            }
                            setTimeout(() => {
                            }, 1000);

                        } else if (error.response.status === 500) {
                            if (error.response.data.message.includes("SQLSTATE")) {
                                console.error('خطای پایگاه داده');

                                async function showAlertSql() {
                                    setTimeout(() => {
                                        alert(error.response.data.message);
                                    }, 200);
                                }

                                showAlertSql();
                            } else {
                                async function showAlert500() {
                                    setTimeout(() => {
                                        alert(error.message + ' '
                                            + error.response.data.message);
                                    }, 200);
                                }

                                showAlert500();
                            }

                        } else {
                            async function showAlert() {
                                setTimeout(() => {
                                    alert(error.message);
                                }, 200);
                            }

                            showAlert();

                        }
                    });
            }
        },
        typeChanged() {
            this.type = document.querySelector('#type').value;
        }
    }
}
</script>
<style>
span i {
    cursor: pointer;
}


</style>
