<template>
  <div id="_user">
    <dt-table
    :columns="user"
    :tableData="userArr"
    navbar="username"
    hoverable
    title>
      <template slot="title">
        {{ $t('admin.users') }}
        <button class="button-primary" @click="openUser()">{{ $t('table.add') }}</button>
      </template>
      <template slot="type" slot-scope="{ data }">
        {{ data.type == 1 ?  $t('table.teacher') : $t('table.admin') }}
      </template>
      <template slot="class_user" slot-scope="{ data }">
        <template v-for="subj in data.class_user">
          {{ lang == 'cn' ? subj.class.cn_name : subj.class.en_name }}:
          {{ lang == 'cn' ? subj.subject.cn_name : subj.subject.en_name }}
          <br :key="(subj.subject_id+subj.class_id)*subj.subject_id" />
        </template>
        <template v-if="data.class_user.length == 0">
          --
        </template>
      </template>
      <template slot="choice" slot-scope="{ data }">
        <button class="button-success" @click="enterChoice(data)"
        :disabled="data.type == 0">
          {{ $t('admin.choice') }}
        </button>
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="button button-primary" @click="openUser(data, false)">{{ $t('admin.edit') }}</button>
        <button class="button button-error" @click="confirmDelete(data.id)"
        style="margin-left: .5rem;">{{ $t('admin.delete') }}</button>
      </template>
    </dt-table>

    <!-- modals -->
    <modal ref="confirmModal" class="delete-modal">
      <div class="modal-body">
        <h5>{{ $t('modal.confirmDelete') }}</h5>
        <button class="button button-error" @click="doDelete"
        style="float: right; margin-top: 3rem;">
          {{ $t('modal.confirm') }}
        </button>
      </div>
    </modal>

    <modal ref="editUser">
      <form @submit.prevent="submitUser">
        <div class="u-full-width username">
          <label for="username">{{ $t('modal.username') }}: </label>
          <input class="u-full-width" type="text" id="username" 
          :disabled="!isAdd"
          v-model="edit.user.username">
        </div>
        <div class="u-full-width cn_name">
          <label for="user_cn_name">{{ $t('modal.cn_name') }}: </label>
          <input class="u-full-width" type="text" id="user_cn_name" 
          v-model="edit.user.cn_name">
        </div>
        <div class="u-full-width en_name">
          <label for="user_en_name">{{ $t('modal.en_name') }}: </label>
          <input class="u-full-width" type="text" id="user_en_name" 
          v-model="edit.user.en_name">
        </div>
        <div class="u-full-width email">
          <label for="user_email">{{ $t('forgot.email') }}: </label>
          <input class="u-full-width" type="text" id="user_email" 
          v-model="edit.user.email">
        </div>
        <div class="u-full-width password">
          <label for="password">{{ $t('modal.password') }}: </label>
          <input class="u-full-width" type="password" id="password" 
          v-model="edit.user.password">
        </div>
        <div class="u-full-width confirm_password">
          <label for="confirm_password">{{ $t('modal.confirm_password') }}: </label>
          <input class="u-full-width" type="password" id="confirm_password" 
          @keyup="checkPassword"
          v-model="confirm_password">
        </div>
        <small style="color: red; margin-bottom: 1rem;" v-if="!isPasswordOk"><em>
          {{ $t('modal.passwordError') }} 
        </em></small>
        <small style="color: red;" v-if="!isAdd"><em>
          * {{ $t('modal.passwordHint') }} 
        </em></small>
        <div class="u-full-width type">
          <label for="user_type">{{ $t('modal.user_type') }}: </label>
          <select name="user_type" id="user_type" v-model="edit.user.type">
            <option :value="-1" disabled>
              {{ $t('modal.user_typeSelect') }}
            </option>
            <option :value="0">
              {{ $t('table.admin') }}
            </option>
            <option :value="1">
              {{ $t('table.teacher') }}
            </option>
          </select>
        </div>

        <input type="submit" class="button button-primary" 
        :disabled="!isPasswordOk"
        style="margin-top: 1rem;"
        :value="$t('modal.submit')">
      </form>
    </modal>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import { user } from '@/api/tableColumns';
import { createUser, getUsers, editUser, deleteUser } from '@/api/users';

import dtTable from '@/components/table';
import modal from '@/components/modal';
export default {
  components: {
    dtTable,
    modal
  },
  mounted() {
    getUsers().then((data) => {
      if (data.status == 200) {
        this.userArr = data.data;
      }
    })
  },
  data: () => ({
    user,
    isAdd: false,
    userArr: [],
    confirm_password: "",
    isPasswordOk: true,
    edit: {
      id: 0,
      user: {
        username: '',
        cn_name: '',
        en_name: '',
        email: '',
        password: '',
        type: -1,
      }
    },
    delete: {
      id: 0,
    },
    empty_user: {
      username: '',
      cn_name: '',
      en_name: '',
      email: '',
      password: '',
      type: -1,
    }
  }),
  methods: {
    ...mapMutations({
      setSub: 'SET_SUBUSER'
    }),
    openUser(data, isAdd = true) {
      this.isAdd = isAdd;
      if (!this.isAdd) this.edit.id = data.id;
      this.edit.user = data || this.empty_user;
      this.confirm_password = "";
      this.isPasswordOk = true;
      if (data) {
        delete this.edit.user.remember_token;
        delete this.edit.user.id;
      }
      this.$refs.editUser.active = true;
    },
    submitUser() {
      if (this.isAdd) {
        if (this.confirm_password != this.edit.user.password) {
          this.isPasswordOk = false;
          return ;
        }
        createUser(this.edit.user).then((data) => {
          if (data.status == 200) {
            this.$refs.editUser.active = false;
            getUsers().then((data) => {
              if (data.status == 200) {
                this.userArr = data.data;
                this.edit.user = this.empty_user;
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        }).catch((err) => {
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error add',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error add',
              text: err.message
            })
        })
      }
      else {
        if (!this.edit.user.password) delete this.edit.user.password;
        delete this.edit.user.username;
        editUser(this.edit.user, this.edit.id).then((data) => {
          if (data.status == 200) {
            this.$refs.editUser.active = false;
            getUsers().then((data) => {
              if (data.status == 200) {
                this.userArr = data.data;
                this.edit.user = this.empty_user;
                this.$nextTick(this.$forceUpdate);
              }
            })
          }
        }).catch((err) => {
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error edit',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error edit',
              text: err.message
            })
        })
      }
    },
    confirmDelete(id) {
      this.$refs.confirmModal.active = true;
      this.delete.id = id;
    },
    doDelete() {
      deleteUser(this.delete.id).then((data) => {
        if (data.status == 200) {
          this.$refs.confirmModal.active = false;
          getUsers().then((data) => {
            if (data.status == 200) {
              this.userArr = data.data;
              this.$nextTick(this.$forceUpdate);
            }
          })
        }
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error delete',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error delete',
            text: err.message
          })
      })
    },
    enterChoice(sub_user) {
      this.setSub(sub_user);
      this.$router.push({ name: 'choice' });
    },
    checkPassword() {
      this.isPasswordOk = this.edit.user.password == this.confirm_password;
    }
  },
  computed: {
    ...mapState({
      lang: 'lang'
    })
  }
}
</script>

<style lang="scss">

</style>
