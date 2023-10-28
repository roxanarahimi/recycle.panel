<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <h3 class="mb-5">ویرایش اسلاید</h3>

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div v-if="isDefined" class="card">
                        <div class="card-body">
                            <div class="col-12 mb-3">
                                <nav>
                                    <div class="nav nav-tabs " id="nav-tab" role="tablist">
                                        <button class="nav-link active w-50" id="nav-home-tab" @click="tabChange('pic')"
                                                data-bs-toggle="tab"
                                                data-bs-target="#nav-home" type="button" role="tab"
                                                aria-controls="nav-home"
                                                aria-selected="true">آپلود تصویر
                                        </button>
                                        <button class="nav-link  w-50" id="nav-profile-tab" @click="tabChange('vdo')"
                                                data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile"
                                                aria-selected="false">آپلود ویدئو
                                        </button>
                                    </div>
                                </nav>

                                <div class="tab-content p-3 border border-top-0  rounded-bottom" id="nav-tabContent">
                                    <div class="tab-pane active fade show" id="nav-home" role="tabpanel"
                                         aria-labelledby="nav-home-tab">
                                        <form id="editForm">
                                            <div class="row">
                                                <label class="form-label">تصویر</label><br/>
                                                <image-cropper name="" :src="data.image" caption=""
                                                               :hasCaption="hasCaption" :isRequired="imgRequired"
                                                               :aspect="aspect"/>
                                                <div id="imageHelp" class="form-text error"></div>
                                                <div class="col-md-12 mb-3">
                                                    <button class="btn btn-primary" @click.prevent="updateInfo"
                                                            type="submit">
                                                        ثبت
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                         aria-labelledby="nav-profile-tab">

                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <video class="w-100 mb-2" width="200" :src="data.video" autoplay
                                                       controls></video>
                                            </div>
                                        </div>
                                        <form id="editForm1" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-12 col-xl-6 mb-3">
                                                    <label class="form-label">ویدیو</label><br/>
                                                    <input id="video" type="file" @change="fileChanged($event)"
                                                           class="form-control" accept="video/mp4">

                                                    <textarea style="opacity: 0;" rows="1" id="code"
                                                              ></textarea>
                                                    <div id="codeHelp" class="form-text error"></div>
                                                    <p class="videoLoading d-none text-danger">لطفا منتظر بمانید. ویدیو
                                                        در حال لود شدن است...</p>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <btn-submit @click.prevent="updateInfo1">
                                                        ثبت
                                                    </btn-submit>
                                                </div>
                                            </div>

                                        </form>

                                    </div>
                                </div>

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
import App from '../App';
// import {toArray} from "../../../public/cropperjs/src/js/utilities";

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
        }
    },

    mounted() {


        this.loadData();

    },

    methods: {
        async loadData() {

            await axios.get('/api/panel/slide/' + this.id)
                .then((response) => {
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

                await axios.post('/api/panel/slide/' + this.$route.params.id,
                    {
                        image: document.getElementById('Image__code').value,
                    })
                    .then((response) => {
                        if (response.status === 200) {
                            setTimeout(() => {
                                // this.$router.push({path: '/panel/slide/'+ this.id });
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
        async updateInfo1() {
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

                await axios.post('/api/panel/video/slide/' + this.$route.params.id,
                    {
                        video: document.getElementById('code').value,
                        // title: document.getElementById('title').value,
                    })
                    .then((response) => {
                        console.log(response)
                        if (response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/slides'});
                            }, 1000);
                        }
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            let errorList = Array(error.response.data);
                            for (var i = 0; i < errorList.length; i++) {
                                this.errors = errorList[i];
                            }
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
        fileChanged(e) {
            let files = e.target.files || e.dataTransfer.files;
            if (files.length) {
                document.querySelector('.videoLoading').classList.remove('d-none');
            }
            if (files[0].type == 'video/mp4') {
                if (files[0].size > 20000000) {
                    document.querySelector('#video').value = null;
                    document.querySelector('#code').value = null;
                    alert('حداکثر سایز مجاز 20 مگابایت میباشد');
                    document.querySelector('.videoLoading').classList.add('d-none');
                } else {
                    let reader = new FileReader();
                    reader.readAsDataURL(files[0]);
                    reader.onloadend = function () {
                        document.querySelector('#code').value = reader.result;
                        document.querySelector('.videoLoading').classList.add('d-none');

                    };
                }

            } else {
                document.querySelector('#video').value = null;
                document.querySelector('#code').value = null;
                alert('فرمت باید mp4 باشد');
                document.querySelector('.videoLoading').classList.add('d-none');
            }
        },
        tabChange(type) {
            switch (type) {
                case 'pic':
                    document.querySelector('#video')?.removeAttribute('required');
                    document.querySelector('#Image_')?.setAttribute('required', 'required');
                    break;
                case 'vdo':
                    document.querySelector('#Image_')?.removeAttribute('required');
                    document.querySelector('#video')?.setAttribute('required', 'required');
                    break;
                // default:
                // code block
            }
        },

    }
}
</script>
<style>
span i {
    cursor: pointer;
}


</style>
