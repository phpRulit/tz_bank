import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    errors: [],
    processing: false,
    success: null,
    reject: null,
  },
  getters: {
    errors: state => state.errors,
    processing: state => state.processing,
    success: state => state.success,
    reject: state => state.reject,
  },
  mutations: {
    setErrors(state, errors) {
      state.errors = errors;
    },
    setProcessing(state, data) {
      state.processing = data;
    },
    setSuccess(state, msg) {
      state.success = msg;
    },
    setReject(state, msg) {
      state.reject = msg;
    },
  },
  actions: {
    sentData({commit}, details) {
      commit('setReject', null);
      commit('setSuccess', null);
      commit('setProcessing', true);
      return axios
          .post('/questionnaire/get-estimate', details)
          .then(({ data }) => {
            if (data.errors) {
              commit("setErrors", data.errors);
            } else if (data.reject) {
              commit('setReject', data.reject);
            } else if (data.success) {
              commit('setSuccess', data.success);
              commit("setErrors", []);
            }
            commit('setProcessing', false);
          }).catch(err => {
            if (err) commit('setProcessing', false);
          })
    }
  },
  modules: {
  }
})
