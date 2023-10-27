<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <h3 class="mb-5">ثبت اسلاید جدید</h3>

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">

                                    <div class="col-12 mb-3">
                                        <nav>
                                            <div class="nav nav-tabs " id="nav-tab"  role="tablist">
                                                <button class="nav-link active w-50" id="nav-home-tab" @click="tabChange('pic')" data-bs-toggle="tab"
                                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                                        aria-selected="true">آپلود تصویر
                                                </button>
                                                <button class="nav-link  w-50" id="nav-profile-tab" @click="tabChange('vdo')" data-bs-toggle="tab"
                                                        data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                                                        aria-selected="false">آپلود ویدئو
                                                </button>
                                            </div>
                                        </nav>

                                        <div class="tab-content p-3 border border-top-0  rounded-bottom" id="nav-tabContent">
                                            <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                                <form id="editForm">
                                                    <div class="row">
                                                        <label class="form-label">تصویر</label><br/>
                                                        <image-cropper name="" caption="" :hasCaption="hasCaption"
                                                                       :isRequired="imgRequired" :aspect="aspect"/>
                                                        <div id="imageHelp" class="form-text error"></div>
                                                        <div class="col-md-12 mb-3">
                                                            <button class="btn btn-primary" @click.prevent="createInfo" type="submit">
                                                                ثبت
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
<!--                                                <form id="editForm1">-->
<!--                                                    <div class="row">-->
<!--                                                    <div class="col-md-6">-->
<!--                                                        <label class="form-label">ویدئو</label><br/>-->
<!--                                                        <input type="file" class="form-control" name="video" id="video">-->
<!--                                                        <div id="videoHelp" class="form-text error"></div>-->
<!--                                                        <div class="col-md-12 mb-3 mt-5">-->
<!--                                                            <button class="btn btn-primary" @click.prevent="createInfo1" type="submit">-->
<!--                                                                ثبت-->
<!--                                                            </button>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    </div>-->
<!--                                                </form>-->
                                                <form id="editForm1"  enctype="multipart/form-data">
                                                    <div class="row">
                                                        <div class="col-12 col-xl-6 mb-3">
                                                            <label class="form-label">ویدیو</label><br/>
                                                            <input id="video" type="file" @change="fileChanged($event)" class="form-control" accept="video/mp4">

                                                            <textarea style="opacity: 0;" rows="1" id="code" required></textarea>
                                                            <div id="codeHelp" class="form-text error"></div>
                                                            <p class="videoLoading d-none text-danger">لطفا منتظر بمانید. ویدیو در حال لود شدن است...</p>

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <btn-submit @click.prevent="createInfo1">
                                                                ثبت
                                                            </btn-submit>
                                                        </div>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">


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
import btnSubmit from "../../components/BtnSubmit";
export default {
    components: {ImageCropper, btnSubmit},
    data: function () {
        return {
            id: '',
            blog: [],
            errors: [],
            image_codes: [],
            image_names: [],
            imgRequired: true,
            hasCaption: false,
            aspect: 1920 / 1080,
        }
    },
    mounted() {

    },
    methods: {
        async createInfo() {
            await App.methods.checkToken();
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

                await axios.post('/api/panel/slide', {
                        image: document.getElementById('Image__code').value,
                        // title: document.getElementById('title').value,
                        // subTitle: document.getElementById('subTitle').value,
                        // link: document.getElementById('link').value,

                    })
                    .then((response) => {
                        if (response.status === 201 || response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/slides'});
                            }, 1000);

                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        console.log(error.message);
                        console.log(error.data);

                        if (error.status === 422) {
                            let errorList = Array(error.data);
                            for (var i = 0; i < errorList.length; i++) {
                                this.errors = errorList[i];
                            }
                            setTimeout(() => {
                            }, 1000);
                        } else if (error.status === 500) {
                            if (error.data.message.includes("SQLSTATE")) {
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

                    })
            }
        },
        // async createInfo1() {
        //     await App.methods.checkToken();
        //     this.errors = [];
        //     let emptyFieldsCount = 0;
        //     let req = document.querySelectorAll('[required]');
        //     req.forEach((element) => {
        //         if (element.value === "") {
        //             element.classList.add('hasError');
        //             element.nextSibling.innerHTML = "فیلد اجباری";
        //             emptyFieldsCount++;
        //         } else {
        //             element.classList.remove('hasError');
        //             element.nextSibling.innerHTML = "";
        //         }
        //     });
        //
        //
        //     if (emptyFieldsCount === 0) {
        //
        //         let formData = new FormData();
        //         formData.append("video", document.getElementById('video').files[0],document.getElementById('video').files[0].name);
        //         console.log('ffffff',formData)
        //         await axios.post('/api/panel/video/slide', formData)
        //             .then((response) => {
        //                 if (response.status === 201 || response.status === 200) {
        //                     setTimeout(() => {
        //                         // this.$router.push({path: '/panel/slides'});
        //                     }, 1000);
        //
        //                 }
        //             })
        //             .catch((error) => {
        //                 console.log(error);
        //                 console.log(error.message);
        //                 console.log(error.data);
        //
        //                 if (error.status === 422) {
        //                     let errorList = Array(error.data);
        //                     for (var i = 0; i < errorList.length; i++) {
        //                         this.errors = errorList[i];
        //                     }
        //                     setTimeout(() => {
        //                     }, 1000);
        //                 } else if (error.status === 500) {
        //                     if (error.data.message.includes("SQLSTATE")) {
        //                         console.error('خطای پایگاه داده');
        //
        //                         async function showAlertSql() {
        //                             setTimeout(() => {
        //                                 alert(error.response.data.message);
        //                             }, 200);
        //                         }
        //
        //                         showAlertSql();
        //                     } else {
        //                         async function showAlert500() {
        //                             setTimeout(() => {
        //                                 alert(error.message + ' '
        //                                     + error.response.data.message);
        //                             }, 200);
        //                         }
        //
        //                         showAlert500();
        //                     }
        //                 } else {
        //
        //                     async function showAlert() {
        //                         setTimeout(() => {
        //                             alert(error.message);
        //                         }, 200);
        //                     }
        //
        //                     showAlert();
        //                 }
        //
        //             })
        //     }
        // },
        async createInfo1() {
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
                await axios.post('/api/panel/video/slide', {
                    video: document.getElementById('code').value,
                })
                    .then((response) => {
                        console.log(response.data)
                        if (response.status === 201 || response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/slides'});
                            }, 1000);

                        }
                    })
                    .catch((error) => {
                        if (error.status === 422) {
                            let errorList = Array(error.response.data);
                            for (var i = 0; i < errorList.length; i++) {
                                this.errors = errorList[i];
                            }
                            console.log(this.errors.toString());
                        } else if (error.status === 500) {
                            if (error.response.data.message.includes("SQLSTATE")) {
                                console.error('خطای پایگاه داده');

                                async function showAlertSql() {
                                    setTimeout(() => {
                                        alert(error.data.message);
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

                    })
            }
        },
        fileChanged(e){
            let files = e.target.files || e.dataTransfer.files;
            if (files.length) {
                document.querySelector('.videoLoading').classList.remove('d-none');
            }
            if(files[0].type == 'video/mp4'){
                if (files[0].size > 20000000){
                    document.querySelector('#video').value = null;
                    document.querySelector('#code').value = null;
                    alert('حداکثر سایز مجاز 20 مگابایت میباشد');
                    document.querySelector('.videoLoading').classList.add('d-none');
                }else{
                    let reader = new FileReader();
                    reader.readAsDataURL(files[0]);
                    reader.onloadend = function () {
                        document.querySelector('#code').value = reader.result;
                        document.querySelector('.videoLoading').classList.add('d-none');

                    };
                }

            }else{
                document.querySelector('#video').value = null;
                document.querySelector('#code').value = null;
                alert('فرمت باید mp4 باشد');
                document.querySelector('.videoLoading').classList.add('d-none');
            }
        },
        tabChange(type){
          switch (type){
              case 'pic':
                  document.querySelector('#video').removeAttribute('required');
                  document.querySelector('#Image_').setAttribute('required','required');
                  break;
              case 'vdo':
                  document.querySelector('#Image_').removeAttribute('required');
                  document.querySelector('#video').setAttribute('required','required');
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


.nav-tabs .nav-link {
    color: black;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    /*background-color: #F8F9FA;*/
    border-bottom: 0 !important;
}

.btn-primary, .btn-primary:focus, .btn-primary:hover {
    /*background-color: darkcyan;*/
}

</style>
