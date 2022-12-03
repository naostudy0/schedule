<template>
  <div class="content">
    <div class="icon-wrap"
      @drop="onAlert"
      @dragover.prevent
    >
      <font-awesome-icon icon="fa-solid fa-trash-can" class="fa-2x"/>
    </div>
    <div class="title">
      <h1>{{ displayDate }}</h1>
      <div class="button-area">
        <button @click="prevMonth">◀︎</button>
        <button @click="nextMonth">▶︎</button>
      </div>
    </div>
    <div
      class="calendar"
      v-if="Object.keys(apiData).length"
    >
      <div class="week">
        <!-- 曜日表示箇所 -->
        <div
          v-for="dayOfWeek in dayOfWeeks"
          class="dayOfWeek"
        >
          {{ dayOfWeek }}
        </div>
      </div>
      <div
        v-for="week in calendars"
        class="week"
      >
        <!-- 日付表示箇所 -->
        <div
          v-for="(day, index) in week"
          :key="index"
          class="calendar-daily"
          :class="{notCurrentMonth: currentMonth !== day.month}"
          @click="currentMonth === day.month ? show(day.date) : ''"
        >
          <!-- 日付 -->
          <div class="date">
            {{ day.day }}
          </div>
          <!-- 予定（共有された予定はドラッグ不可） -->
          <div
            v-for="plan in day.plans"
            :key="plan.planId"
            class="plan"
            :style="`width:${plan.width}%;background-color:${plan.color}`"
            :draggable="! (plan.planMadeUserId !== null && plan.planMadeUserId !== apiData.data.userId)"
            @drag="setDragPlanId(plan.planId)"
            @click.stop="update(plan)"
          >
            {{ (plan.startDatetime.substr(0, 10) === day.date || day.dayOfWeekNum === 0) ? plan.content : '' }}
          </div>
        </div>
      </div>
    </div>
    <!-- モーダル -->
    <modal name="makePlanArea" style="height: 350px;">
      <form onsubmit="return false">
        <!-- tokenはaxiosで設定されるため記載しない -->
        <div
          class="modal-header"
          :style="`background-color:${modalColor}`"
        >
          <h2 v-if="updatePlanId === 0">
            予定入力
          </h2>
          <h2 v-else>
            予定更新
          </h2>
        </div>
        <div
          class="modal-body"
          v-if="Object.keys(apiData).length"
        >
          <div class="datetime-modal">
            <div class="start-datetime-modal">
              <label>開始日時
                <input
                  type="date"
                  name="startDate"
                  v-model="modalData.startDate"
                  :readonly="isSharedPlan"
                  @blur="validateDateTime()"
                ></label>
              <input
                type="time"
                name="startTime"
                v-model="modalData.startTime"
                :readonly="isSharedPlan"
                @blur="validateDateTime()"
              >
            </div>
            <div class="end-datetime-modal">
              <label>終了日時
                <input
                  type="date"
                  name="endDate"
                  v-model="modalData.endDate"
                  :readonly="isSharedPlan"
                  @blur="validateDateTime()"
                ></label>
              <input
                type="time"
                name="endTime"
                v-model="modalData.endTime"
                :readonly="isSharedPlan"
                @blur="validateDateTime()"
              >
            </div>
          </div>
          <div v-if="! validate.datetime">
            <span class="error">
              終了日時は開始時間より後の日時を入力してください
            </span>
          </div>

          <!-- 共有された予定の場合は非表示 -->
          <div
            v-if="! isSharedPlan"
            class="color-modal"
          >
            <span>カラー</span>
            <span
              v-for="(color, index) in apiData.data.colorsFlip"
              :key="index"
            >
            <label class="modal-color-item">
                <input
                  name="color"
                  type="radio"
                  :value="index"
                  v-model="colorNum"
                >
                {{ color }}
              </label>
            </span>
          </div>

          <div class="content-modal">
            <label>内容
              <input
                type="text"
                name="content"
                v-model="modalData.content"
                :readonly="isSharedPlan"
                @blur="validateContent()"
              >
            </label>
            <div v-if="! validate.content">
              <span class="error">
                内容は必須です
              </span>
            </div>
          </div>

          <div class="detail-modal">
            <label>詳細
              <textarea
                name="detail"
                :readonly="isSharedPlan"
              >{{ modalData.detail }}</textarea>
            </label>
          </div>

          <div v-if="isSharedPlan">
            予定共有
            <span v-for="shareUser in apiData.data.shareUsers">
              <label>
                <input
                  type="checkbox"
                  name="shareUser"
                  :value="shareUser.share_id"
                >
                {{ shareUser.name }}
              </label>
            </span>
          </div>

          <div
            v-if="apiData.data.sharedUserNames[updatePlanId] !== undefined"
          >
            <span
              v-if="updatePlanId !== 0 && modalData.planMadeUserId !== apiData.data.userId"
            >
              この予定は、{{ modalData.planMadeUserName }} さんに共有されています
            </span>
          </div>

          <div
            v-if="apiData.data.sharedUserNames[updatePlanId] !== undefined"
          >
            <ul v-for="(userName, userId) in apiData.data.sharedUserNames[updatePlanId]"
              :key="userId"
            >
              <li v-if="userId != apiData.data.userId">
                {{ userName }}さん
              </li>
            </ul>
            <span
              v-if="Object.keys(apiData.data.sharedUserNames[updatePlanId]).length === 1
                && modalData.planMadeUserId === apiData.data.userId"
            >
            <!-- 共有が一人で作成者が自分ではない場合は、自分だけが共有された人になり名前を表示しないので以下も非表示 -->
              と共有しています
            </span>
          </div>

          <span v-if="updatePlanId === 0">
            <button
              class="forward"
              @click="isAfterInput ? storePlan() : validateBeforeInput()"
            >登録</button>
            <button
              class="back"
              @click="hide"
            >キャンセル</button>
          </span>
          <span v-else>
            <span v-if="isSharedPlan">
              <!-- 自分が作成していない予定は更新させない -->
              <button
                class="back"
                @click="hide"
              >閉じる</button>
            </span>
            <span v-else>
              <button
                class="forward"
                @click="isAfterInput ? updatePlan() : validateBeforeInput()"
              >更新</button>
              <button
                class="back"
                @click="hide"
              >キャンセル</button>
            </span>
          </span>
        </div>
      </form>
    </modal>
  </div>
</template>

<script>
import moment from "moment"

export default {
  data() {
    return {
      currentDate: moment(),
      calendars: [],
      dayOfWeeks: ['日', '月', '火', '水', '木', '金', '土'],
      apiData: {},
      modalData: {},
      dragPlanId: '',
      colorNum: 1,
      updatePlanId: 0,
      validate: {
        datetime: true,
        content: true,
      },
      beforeInput: true
    };
  },
  methods: {
    /**
     * APIでデータを取得
     * @return {void}
     */
    async getApiData() {
      this.apiData = await axios.get('/api/schedule/?date=' + this.currentMonth)
    },
    /**
     * カレンダーの最初の日付（日曜）を取得
     * @return {object}
     */
    getStartDate() {
      // 月初の日付
      let startOfMonth = moment(this.currentDate).startOf('month')
      // 月初の日付の曜日番号
      let dayOfWeekNum = startOfMonth.day()

      return startOfMonth.subtract(dayOfWeekNum, 'd')
    },
    /**
     * カレンダーの最後の日付（土曜）を取得
     * @return {object}
     */
    getEndDate() {
      // 月末の日付
      let endOfMonth = moment(this.currentDate).endOf("month")
      // 月末の日付の曜日番号
      let dayOfWeekNum = endOfMonth.day()

      return endOfMonth.add(6 - dayOfWeekNum, 'd')
    },
    /**
     * カレンダーに表示する日付と予定を設定
     * @return {void}
     */
    getCalendar() {
      // APIでデータを取得
      this.getApiData()
      .then(response => {
        // カレンダーの最初の日付（日曜）
        let startDate = this.getStartDate()
        // カレンダーの最後の日付（土曜）
        let endDate = this.getEndDate()
        // 何週間か計算
        let weekCount = Math.ceil(endDate.diff(startDate, 'd') / 7)
        // 再描画時のために再度初期化
        this.calendars = []

        let tmpDate = startDate
        for (let week = 0; week < weekCount; week++) {
          let tmpWeekData = []
          for (let dayOfWeekNum = 0; dayOfWeekNum < 7; dayOfWeekNum++) {
            tmpWeekData.push({
              month: tmpDate.format('YYYY-MM'),
              date:  tmpDate.format('YYYY-MM-DD'),
              day:   tmpDate.format('D'),
              dayOfWeekNum: dayOfWeekNum,
              // 当月の場合のみ予定を取得
              plans: tmpDate.format('YYYY-MM') === this.currentMonth
                      ? this.getDayEvents(tmpDate.format('YYYY-MM-DD'), dayOfWeekNum)
                      : [],
            });
            tmpDate.add(1, 'd')
          }
          this.calendars.push(tmpWeekData)
        }
      })
    },
    /**
     * 対象の日の予定を取得
     * @param {string} date         日付
     * @param {int}    dayOfWeekNum 曜日番号
     */
    getDayEvents(date, dayOfWeekNum) {
      let stackIndex = 0;
      let dayEvents = [];
      let startedEvents = [];

      Object.values(this.apiData.data.planData).forEach(event => {
        let startDate = moment(event.startDatetime).format('YYYY-MM-DD')
        let endDate = moment(event.endDatetime).format('YYYY-MM-DD')
        let Date = date
        if (startDate <= Date && endDate >= Date) {
          if (startDate === Date || dayOfWeekNum === 0) {
            [stackIndex, dayEvents] = this.getStackEvents(event, dayOfWeekNum, stackIndex, dayEvents, startedEvents, Date);
          } else {
            startedEvents.push(event)
          }
        }
      });

      return dayEvents;
    },
    /**
     * 対象の日の予定と個数を取得
     * @param {object} event
     * @param {int}    dayOfWeekNum
     * @param {int}    stackIndex
     * @param {array}  dayEvents
     * @param {array}  startedEvents
     * @param {string} start
     */
    getStackEvents(event, dayOfWeekNum, stackIndex, dayEvents, startedEvents, start){
      [stackIndex, dayEvents] = this.getStartedEvents(stackIndex, startedEvents, dayEvents)
      let width = this.getEventWidth(start, event.endDatetime, dayOfWeekNum)
      Object.assign(event, {
        stackIndex
      })
      dayEvents.push({...event, width})
      stackIndex++;

      return [stackIndex,dayEvents]
    },
    /**
     * 開始された予定と個数を取得
     * @param  {int}   stackIndex
     * @param  {array} startedEvents
     * @param  {array} dayEvents
     * @return {int, object}
     */
    getStartedEvents(stackIndex, startedEvents, dayEvents){
      let startedEvent;
      do {
        startedEvent = startedEvents.find(event => event.stackIndex === stackIndex)
        if (startedEvent) {
          dayEvents.push(startedEvent)
          stackIndex++;
        }
      } while (startedEvent !== void 0)

      return [stackIndex, dayEvents]
    },
    /**
     * 予定のwidthを設定
     * @param  {string} start
     * @param  {string} end
     * @param  {int}    day
     * @return {int}
     */
    getEventWidth(start, end, day) {
      let diffDays = moment(end).diff(moment(start), 'd')
      let width = 0

      if (diffDays > 6 - day) {
        width = (6 - day) * 100 + 95
      } else {
        width = diffDays * 100 + 95
      }

      return width
    },
    /**
     * 次月のカレンダーを表示
     * @return {void}
     */
    nextMonth() {
      this.currentDate = moment(this.currentDate).add(1, "month")
      // 月の表示が切り替わるときに前のカレンダーの表示が残ってしまうため初期化
      this.apiData = {}
      this.getCalendar()
    },
    /**
     * 前月のカレンダーを表示
     * @return {void}
     */
    prevMonth() {
      this.currentDate = moment(this.currentDate).subtract(1, "month")
      // 月の表示が切り替わるときに前のカレンダーの表示が残ってしまうため初期化
      this.apiData = {}
      this.getCalendar()
    },
    /**
     * モーダル表示
     * @param  {string} date
     * @return {void}
     */
    show(date) {
      this.modalData = {
        startDate: date,
        startTime: '00:00',
        endDate: date,
        endTime: '00:00',
        color: 1,
        content: '',
        detail: '',
        planMadeUserId: 0,
        planMadeUserName: '',
      }

      // 更新予定IDの初期化
      this.updatePlanId = 0

      // バリデーションの初期化
      this.validate.datetime = true
      this.validate.content = true
      this.beforeInput = true

      // モーダルで選択状態にする色番号
      this.colorNum = this.modalData.color
      this.$modal.show('makePlanArea');
    },
    /**
     * 予定更新
     * @return {void}
     */
    update(plan) {
      this.modalData = {
        startDate: moment(plan.startDatetime).format('YYYY-MM-DD'),
        startTime: moment(plan.startDatetime).format('HH:mm'),
        endDate:   moment(plan.endDatetime).format('YYYY-MM-DD'),
        endTime:   moment(plan.endDatetime).format('HH:mm'),
        color:            plan.color,
        content:          plan.content,
        detail:           plan.detail,
        planMadeUserId:   plan.planMadeUserId,
        planMadeUserName: plan.planMadeUserName,
      }

      // 更新する予定ID（フォームに入れる必要がないので別で設定）
      this.updatePlanId = plan.planId

      // バリデーションの初期化
      this.validate.datetime = true
      this.validate.content = true
      this.beforeInput = false

      // モーダルで選択状態にする色番号
      this.colorNum = this.apiData.data.colors[plan.color]
      this.$modal.show('makePlanArea')
    },
    /**
     * モーダル非表示
     * @return {void}
     */
    hide() {
      this.$modal.hide('makePlanArea')
    },
    /**
     * 予定保存
     * @return {void}
     */
    async storePlan() {
      await axios.post('/api/schedule/store', this.getFormData())
      .then(response => {
        this.getCalendar()
      })
      .then(response => {
        this.hide()
      })
      .catch(response => response)
    },
    /**
     * 予定更新
     * @return {void}
     */
     async updatePlan() {
      await axios.post('/api/schedule/update', this.getFormData())
      .then(response => {
        this.getCalendar()
      })
      .then(response => {
        this.hide()
      })
      .catch(response => response)
    },
    /**
     * フォームの入力値を取得
     * @return {object}
     */
    getFormData() {
      let elementsColor = document.getElementsByName('color')
      let lenColor = elementsColor.length
      let checkValue = 0;
      for (let i = 0; i < lenColor; i++) {
        if (elementsColor.item(i).checked) {
          checkValue = elementsColor.item(i).value;
        }
      }

      let elementsShareUser = document.getElementsByName('shareUser')
      let lenShareUser = elementsShareUser.length
      let checkArr = []
      for (let i = 0; i < lenShareUser; i++) {
        if (elementsShareUser.item(i).checked) {
          checkArr.push(elementsShareUser.item(i).value)
        }
      }

      return {
        start_date: document.getElementsByName('startDate').item(0).value,
        start_time: document.getElementsByName('startTime').item(0).value,
        end_date:   document.getElementsByName('endDate').item(0).value,
        end_time:   document.getElementsByName('endTime').item(0).value,
        color:      checkValue,
        content:    document.getElementsByName('content').item(0).value,
        detail:     document.getElementsByName('detail').item(0).value,
        plan_id:    this.updatePlanId,
        share_user: checkArr,
      }
    },
    /**
     * ドラッグしている予定のIDを設定
     * @param  {int} planId
     * @return {void}
     */
    setDragPlanId(planId) {
      this.dragPlanId = planId
    },
    /**
     * 予定削除の確認画面表示
     * @return {void}
     */
    onAlert() {
      this.$dialog
      .confirm({
        title: '予定削除',
        body: '予定を削除してもよろしいですか？'
      }, {
        okText: 'はい',
        cancelText: 'キャンセル',
      })
      .then(response => {
        axios.get('/api/schedule/destroy/' + this.dragPlanId)
      })
      .then(response => {
        // 予定削除後にカレンダー再描画
        this.getCalendar()
      })
      .catch(response => response)
    },
    /**
     * 日付のバリデーション
     * @return {void}
     */
    validateDateTime() {
      // 未入力フラグの更新
      this.beforeInput = false

      let startDate = document.getElementsByName('startDate').item(0).value
      let startTime = document.getElementsByName('startTime').item(0).value
      let endDate   = document.getElementsByName('endDate').item(0).value
      let endTime   = document.getElementsByName('endTime').item(0).value

      let startDateTime = moment(startDate + ' ' + startTime)
      let endDateTime   = moment(endDate + ' ' + endTime)

      this.validate.datetime = startDateTime.isSameOrBefore(endDateTime)
    },
    /**
     * 内容のバリデーション
     * @return {void}
     */
    validateContent() {
      // 未入力フラグの更新
      this.beforeInput = false

      let content = document.getElementsByName('content').item(0).value

      this.validate.content = content !== ''
    },
    /**
     * フォーム未入力で送信ボタンを押したときにバリデーションする
     * @return {void}
     */
    validateBeforeInput() {
      this.validateDateTime()
      this.validateContent()
    }
  },
  created: function () {
    // 初回のカレンダー作成
    this.getCalendar()
  },
  computed: {
    /**
     * 画面に表示する年月
     * @return {string}
     */
    displayDate() {
      return this.currentDate.format('YYYY[年]M[月]')
    },
    /**
     * 現在表示している年月（当月か判定で使用）
     * @return {string}
     */
    currentMonth() {
      return this.currentDate.format('YYYY-MM')
    },
    /**
     * モーダルで選択状態にする色
     * @return {string}
     */
    modalColor() {
      return Object.keys(this.apiData).length ? this.apiData.data.colorsFlip[this.colorNum] : 'white'
    },
    /**
     * フォーム入力が完了したか判定
     * @return {bool}
     */
    isAfterInput() {
      // 描画前のエラー対策と未入力の場合は登録できないようにする
      if (! Object.keys(this.apiData).length || this.beforeInput) {
        return false
      }
      // バリデーションエラーがなければtrue
      return this.validate.content && this.validate.datetime
    },
    /**
     * 共有された予定か判定
     * @return {bool}
     */
    isSharedPlan() {
      if (this.modalData.planMadeUserId === 0) {
        return false
      }
      return this.modalData.planMadeUserId && this.modalData.planMadeUserId !== this.apiData.data.userId
    }
  },
}
</script>
