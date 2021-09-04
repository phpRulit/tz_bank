<template>
  <div class="col-md-6 mx-auto mt-3 mb-5">
    <h2>Анкета для получения кредита</h2>
    <div class="form-group">
      <label for="surname">Фамилия</label>
      <input type="text" id="surname" class="form-control" :class="{ 'is-invalid': errors.surname }" placeholder="Введите фамилию..." v-model="details.surname">
      <div class="invalid-feedback" v-if="errors.surname">
        {{ errors.surname[0] }}
      </div>
    </div>
    <div class="form-group">
      <label for="name">Имя</label>
      <input type="text" id="name" class="form-control" :class="{ 'is-invalid': errors.name }" placeholder="Введите фамилию..." v-model="details.name">
      <div class="invalid-feedback" v-if="errors.name">
        {{ errors.name[0] }}
      </div>
    </div>
    <div class="form-group">
      <label for="patronymic">Отчество</label>
      <input type="text" id="patronymic" class="form-control" :class="{ 'is-invalid': errors.patronymic }" placeholder="Введите фамилию..." v-model="details.patronymic">
      <div class="invalid-feedback" v-if="errors.patronymic">
        {{ errors.patronymic[0] }}
      </div>
    </div>
    <div class="form-group">
      <label for="sex">Пол</label>
      <select id="sex" class="form-control" :class="{ 'is-invalid': errors.sex }" v-model="details.sex">
        <option :value="null">Сделайте выбор...</option>
        <option :value="status.value" v-for="(status, index) in sex" :key="index">{{status.text}}</option>
      </select>
      <div class="invalid-feedback" v-if="errors.sex">{{ errors.sex[0] }}</div>
    </div>
    <div class="form-group">
      <b-row>
        <div class="col-md-12 m-0 p-0 pl-3 pb-1">
          <label class="p-0 m-0">Дата рождения :</label>
        </div>
        <div class="invalid-feedback" v-if="errors.birthday"> {{ errors.birthday[0] }}</div>
        <b-col md="auto">
          <b-calendar
              :class="{ 'border border-danger': errors.birthday }"
              block
              hide-header
              label-prev-year="Предыдущий год"
              label-prev-month="Предыдущий месяц"
              label-current-month="Текущий месяц"
              label-next-month="Следующий месяц"
              label-next-year="Следующий год"
              v-model="details.birthday" :max="max" @context="onContext" locale="ru"
          ></b-calendar>
        </b-col>
      </b-row>
    </div>
    <div class="form-group">
      <label for="minor_children">Количество несовершеннолетних детей</label>
      <input type="number" id="minor_children" class="form-control" min="0" max="30" :class="{ 'is-invalid': errors.minor_children }" placeholder="Введите количество..." v-model="details.minor_children">
      <div class="invalid-feedback" v-if="errors.minor_children">
        {{ errors.minor_children[0] }}
      </div>
    </div>
    <div class="form-group">
      <label for="family_status">Семейное положение</label>
      <select id="family_status" class="form-control" :class="{ 'is-invalid': errors.family_status }" v-model="details.family_status">
        <option :value="null">Сделайте выбор...</option>
        <option :value="status.value" v-for="(status, index) in family_statuses" :key="index">{{status.text}}</option>
      </select>
      <div class="invalid-feedback" v-if="errors.family_status">{{ errors.family_status[0] }}</div>
    </div>
    <div class="form-group">
      <label for="monthly_income">Ежемесячный доход</label>
      <input type="number" id="monthly_income" class="form-control" min="0" :class="{ 'is-invalid': errors.monthly_income }" placeholder="Введите сумму..." v-model="details.monthly_income">
      <div class="invalid-feedback" v-if="errors.monthly_income">
        {{ errors.monthly_income[0] }}
      </div>
    </div>
    <div class="form-group">
      <label>Тип занятости</label>
      <select class="form-control" :class="{ 'is-invalid': errors.type_employment }" v-model="details.type_employment">
        <option :value="null">Сделайте выбор...</option>
        <option :value="status.value" v-for="(status, index) in type_employment" :key="index">{{status.text}}</option>
      </select>
      <div class="invalid-feedback" v-if="errors.type_employment">{{ errors.type_employment[0] }}</div>
    </div>
    <div class="form-group">
      <label for="availability_real_estate">Есть ли недвижимость</label>
      <select id="availability_real_estate" class="form-control" :class="{ 'is-invalid': errors.availability_real_estate }" v-model="details.availability_real_estate">
        <option :value="null">Сделайте выбор...</option>
        <option :value="status.value" v-for="(status, index) in boolean_choice" :key="index">{{status.text}}</option>
      </select>
      <div class="invalid-feedback" v-if="errors.availability_real_estate">{{ errors.availability_real_estate[0] }}</div>
    </div>
    <div class="form-group">
      <label for="outstanding_loans">Есть ли непогашенные кредиты?</label>
      <select id="outstanding_loans" class="form-control" :class="{ 'is-invalid': errors.outstanding_loans }" v-model="details.outstanding_loans">
        <option :value="null">Сделайте выбор...</option>
        <option :value="status.value" v-for="(status, index) in boolean_choice" :key="index">{{status.text}}</option>
      </select>
      <div class="invalid-feedback" v-if="errors.outstanding_loans">{{ errors.outstanding_loans[0] }}</div>
    </div>
    <div class="form-group" v-if="details.outstanding_loans">
      <label for="outstanding_current_loans">Есть ли задолженности по текущим кредитам?</label>
      <select id="outstanding_current_loans" class="form-control" :class="{ 'is-invalid': errors.outstanding_current_loans }" v-model="details.outstanding_current_loans">
        <option :value="null">Сделайте выбор...</option>
        <option :value="status.value" v-for="(status, index) in boolean_choice" :key="index">{{status.text}}</option>
      </select>
      <div class="invalid-feedback" v-if="errors.outstanding_current_loans">{{ errors.outstanding_current_loans[0] }}</div>
    </div>
    <div class="form-group" v-if="details.outstanding_loans">
      <label for="monthly_payment_current_loans">Ежемесячная выплата по текущим кредитам?</label>
      <input type="number" id="monthly_payment_current_loans" class="form-control" min="0" :class="{ 'is-invalid': errors.monthly_payment_current_loans }" placeholder="Введите сумму..." v-model="details.monthly_payment_current_loans">
      <div class="invalid-feedback" v-if="errors.monthly_payment_current_loans">
        {{ errors.monthly_payment_current_loans[0] }}
      </div>
    </div>
    <div class="form-group">
      <button type="button" :disabled="this.processing" @click="getSentData" class="btn btn-primary">Отправить данные</button>
    </div>
  </div>
</template>


<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: 'Form',
  data () {
    //b-calendar
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const maxDate = new Date(today);
    return {
      details: {
        surname: '',
        name: '',
        patronymic: '',
        sex: null,
        birthday: null,
        minor_children: null,
        family_status: null,
        monthly_income: null,
        type_employment: null,
        availability_real_estate: null,
        outstanding_loans: null,
        outstanding_current_loans: null,
        monthly_payment_current_loans: null,
      },
      sex:[
        {value: 0, text: 'Женщина'},
        {value: 1, text: 'Мужчина'},
      ],
      family_statuses:[
        {value: 0, text: 'Холост / Не замужем'},
        {value: 1, text: 'Женат / Замужем'},
      ],
      type_employment:[
        {value: 0, text: 'Не работаю'},
        {value: 1, text: 'Договор'},
        {value: 2, text: 'Самозанятый'},
        {value: 3, text: 'ИП'},
      ],
      boolean_choice: [
        {value: false, text: 'Нет'},
        {value: true, text: 'Да'},
      ],
      max: maxDate
    }
  },
  computed: {
    ...mapGetters(["errors"]),
    ...mapGetters(["processing"]),
  },
  methods: {
    onContext(ctx) {
      this.context = ctx
    },
    ...mapActions(["sentData"]),
    getSentData () {
      this.sentData(this.details)
      .then(() => {
        if (this.$store.getters["reject"]) {
          this.$toastr.e(this.$store.getters["reject"]);
        } else if (this.$store.getters["success"]) {
          this.$toastr.s(this.$store.getters["success"]);
        }
      })
    },
  }
}
</script>

<style scoped>
label {
  font-weight: bold;
}
</style>
