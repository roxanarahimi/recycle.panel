<template>
    <transition name="route" mode="out-in" appear>
        <section>
            <!--            <h3 class="mb-5">ویرایش محصول</h3>-->

            <div class="row mt-3">
                <div class="col-12 mb-3">
                    <div class="card" v-if="isDefined">
                        <div class="card-body">
                            <div id="editForm">
                                <div class="form">

                                    <h3 class="text-center my-5">ویرایش اطلاعات</h3>
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-5" style="font-size: 20px ; padding: 50px; line-height: 30px">

                                            یک کیلو کاغذ
                                            <input @input="updateData" type="text" id="paper" :value="data.paper" class="form-control input"
                                                   required>
                                            تومان

                                            <br>
                                            یک کیلو مقوا
                                            <input @input="updateData" type="text" id="cardboard" :value="data.cardboard"
                                                   class="form-control input" required>
                                            تومان
                                            <br>

                                            <input @input="updateData" type="text" id="paper_cardboard_weight" :value="data.paper_cardboard_weight"
                                                   class="form-control input" required>
                                            کیلوگرم کاغـذ و مقــوا
                                            <input @input="updateData" type="text" id="paper_cardboard_price" :value="data.paper_cardboard_price"
                                                   class="form-control input" required>
                                            تومان


                                        </div>
                                        <div class="col-5" style="font-size: 20px ; padding: 50px; line-height: 30px">
                                            یک کیلو شیشه
                                            <input @input="updateData" type="text" id="glass" class="form-control input" :value="data.glass"
                                                   required>
                                            تومان

                                            <br>
                                            یک کیلو پلاستیک
                                            <input @input="updateData" type="text" id="plastic" :value="data.plastic"
                                                   class="form-control input" required>
                                            تومان

                                            <br>

                                            <input @input="updateData" type="text" id="glass_plastic_weight" :value="data.glass_plastic_weight"
                                                   class="form-control input" required>
                                            کیلوگرم شیشه و پلاستیک
                                            <input @input="updateData" type="text" id="glass_plastic_price" :value="data.glass_plastic_price"
                                                   class="form-control input" required>
                                            تومان


                                        </div>
                                        <div class="col-10" style="font-size: 20px ; padding: 50px; line-height: 30px">
                                            تاخیر بین اسلاید ها
                                            <input @input="updateData" type="text" id="slider_delay" :value="data.slider_delay/1000"
                                                   class="form-control input" required>
                                            ثانیه
                                        </div>
                                        <div class="col-12 text-center">
                                            <button class="btn btn-primary m-2" @click="updateInfo">ویرایش اطلاعات
                                            </button>
                                        </div>

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
import BtnSubmit from "../../components/BtnSubmit";
import Multiselect from '@vueform/multiselect'


export default {
    components: {ImageCropper, App, BtnSubmit, Multiselect},
    data() {
        return {
            id: this.$route.params.id,
            data: {},
            errors: [],
            isDefined: false,
            enableClick: true,


        }
    },

    created() {
        this.loadInfo();
    },

    methods: {
        loadInfo() {
            this.isDefined = true;
            axios.get('/api/panel/site')
                .then((response) => {
                    this.data = response.data;
                 })
                .then(() => {
                    this.isDefined = true;
                })
                .then(() => {
                    this.watchTextAreas();
                })
                .catch();
        },
        updateInfo() {
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

                axios.post('/api/panel/site/' + this.data.id,
                    {
                        // image: document.getElementById('Image_index_code').value,
                        id: this.$route.params.id,
                        paper: document.getElementById('paper').value,
                        cardboard: document.getElementById('cardboard').value,
                        paper_cardboard_weight: document.getElementById('paper_cardboard_weight').value,
                        paper_cardboard_price: document.getElementById('paper_cardboard_price').value,
                        glass: document.getElementById('glass').value,
                        plastic: document.getElementById('plastic').value,
                        glass_plastic_weight: document.getElementById('glass_plastic_weight').value,
                        glass_plastic_price: document.getElementById('glass_plastic_price').value,
                        slider_delay: document.getElementById('slider_delay').value*1000,
                    })
                    .then((response) => {
                        console.log('res', response);
                        if (response.status === 200) {
                            setTimeout(() => {
                                this.$router.push({path: '/panel/site/'});
                            }, 1000);
                        }
                    })
                    .catch((error) => {
                        document.querySelector('#submit').removeAttribute('disabled');
                        document.querySelector('.loader-sm').classList.add('d-none');

                        if (error.response.status === 422) {
                            let errorList = Array(error.response.data);
                            for (var i = 0; i < errorList.length; i++) {
                                this.errors = errorList[i];
                            }

                        } else if (error.response.status === 500) {
                            if (error.response.data.message.includes("SQLSTATE")) {
                                console.error('خطای پایگاه داده');

                                function showAlertSql() {
                                    setTimeout(() => {
                                        alert(error.response.data.message);
                                    }, 200);
                                }

                                showAlertSql();
                            } else {
                                function showAlert500() {
                                    setTimeout(() => {
                                        alert(error.message + ' '
                                            + error.response.data.message);
                                    }, 200);
                                }

                                showAlert500();
                            }

                        } else {
                            function showAlert() {
                                setTimeout(() => {
                                    alert(error.message);
                                }, 200);
                            }

                            showAlert();

                        }
                    });
            }
        },
        updateData() {
            // this.data.title = document.getElementById('title').value;
                this.data.paper = document.getElementById('paper').value;
                this.data.cardboard = document.getElementById('cardboard').value;
                this.data.paper_cardboard_weight = document.getElementById('paper_cardboard_weight').value;
                this.data.paper_cardboard_price = document.getElementById('paper_cardboard_price').value;
                this.data.glass = document.getElementById('glass').value;
                this.data.plastic = document.getElementById('plastic').value;
                this.data.glass_plastic_weight = document.getElementById('glass_plastic_weight').value;
                this.data.glass_plastic_price = document.getElementById('glass_plastic_price').value;
                this.data.slider_delay = document.getElementById('slider_delay').value*1000;
        },

    }
}
</script>

<style>
span i {
    cursor: pointer;
}

.en {
    direction: ltr !important;
    text-align: left !important;
}

.multiselect-tags-search {
    background-color: transparent !important;
}

.multiselect-tag {
    background-color: #0d6efd !important;
}

.multiselect.is-active {
    box-shadow: none !important;
}

.input {
    width: 80px !important;
    display: inline !important;
    height: 35px;
    margin: 5px;
    text-align: center;
    direction: ltr;
    /*border: 2px dotted blue !important;*/
}
</style>
