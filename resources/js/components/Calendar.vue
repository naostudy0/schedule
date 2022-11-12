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
        <button @click="prevMonth">前月</button>
        <button @click="nextMonth">翌月</button>
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
          <!-- 予定 -->
          <div
            v-for="plan in day.plans"
            :key="plan.planId"
            class="plan"
            :style="`width:${plan.width}%;background-color:${plan.color}`"
            draggable="true"
            @drag="setDragPlanId(plan.planId)"
            @click.stop="update(plan)"
          >
            {{ (plan.startDatetime.substr(0, 10) === day.date || day.dayOfWeekNum === 0) ? plan.content : '' }}
          </div>
        </div>
      </div>
    </div>
    <!-- モーダル -->
    <modal name="makePlanArea">
      <form onsubmit="return false">
        <!-- tokenはaxiosで設定されるため記載しない -->
        <div
          class="modal-header"
          :style="`background-color:${modalColor}`"
        >
          <h2>予定入力</h2>
        </div>
        <div class="modal-body">
          <div>
            <label>開始日時<input
              type="date"
              name="start_date"
              v-model="modalData.startDate"
              @blur="validateDateTime()"
            ></label>
            <input
              type="time"
              name="start_time"
              v-model="modalData.startTime"
              @blur="validateDateTime()"
            >
            <label>終了日時<input
              type="date"
              name="end_date"
              v-model="modalData.endDate"
              @blur="validateDateTime()"
            ></label>
            <input
              type="time"
              name="end_time"
              v-model="modalData.endTime"
              @blur="validateDateTime()"
            >
          </div>
          <div v-if="! validate.datetime">
            <span class="error">
              終了日時は開始時間より後の日時を入力してください
            </span>
          </div>

          <span>カラー</span>
          <div v-if="Object.keys(apiData).length">
            <span
              v-for="(color, index) in apiData.data.colorsFlip"
              :key="index"
            >
            <label>
                <input
                  name="color"
                  type="radio"
                  :value="index"
                  v-model="colorNum"
                >
                {{ index + ': ' + color }}
              </label>
            </span>
          </div>

          <div>
            <label>内容
              <input
                type="text"
                name="content"
                v-model="modalData.content"
                @blur="validateContent()"
              >
            </label>
          </div>
          <div v-if="! validate.content">
            <span class="error">
              内容は必須です
            </span>
          </div>

          <div>
            <label>詳細
              <textarea
                name="detail"
              >{{ modalData.detail }}</textarea>
            </label>
          </div>
          <span v-if="updatePlanId === 0">
            <button @click="isAfterInput ? storePlan() : validateBeforeInput()">登録</button>
          </span>
          <span v-else>
            <button @click="isAfterInput ? updatePlan() : validateBeforeInput()">更新</button>
          </span>
          <button @click="hide">キャンセル</button>
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
        color:     plan.color,
        content:   plan.content,
        detail:    plan.detail,
      }

      // 更新する予定ID（フォームに入れる必要がないので別で設定）
      this.updatePlanId = plan.planId

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
      let elements = document.getElementsByName('color');
      let len = elements.length;
      let checkValue = 0;

      for (let i = 0; i < len; i++) {
        if (elements.item(i).checked) {
          checkValue = elements.item(i).value;
        }
      }

      return {
        start_date: document.getElementsByName('start_date').item(0).value,
        start_time: document.getElementsByName('start_time').item(0).value,
        end_date:   document.getElementsByName('end_date').item(0).value,
        end_time:   document.getElementsByName('end_time').item(0).value,
        color:      checkValue,
        content:    document.getElementsByName('content').item(0).value,
        detail:     document.getElementsByName('detail').item(0).value,
        plan_id:    this.updatePlanId,
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

      let startDate = document.getElementsByName('start_date').item(0).value
      let startTime = document.getElementsByName('start_time').item(0).value
      let endDate   = document.getElementsByName('end_date').item(0).value
      let endTime   = document.getElementsByName('end_time').item(0).value

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

      // エラーがなければtrue
      return this.validate.content && this.validate.datetime
    }
  },
}
</script>

<style scoped>
.content{
  margin:2em auto;
  width:900px;
}

.button-area{
  margin:0.5em 0;
}

.calendar{
  max-width:900px;
  border-top:1px solid #E0E0E0;
  font-size:0.8em;
}

.week {
  display:flex;
  border-left:1px solid #E0E0E0;
}

.calendar-daily{
  flex:1;
  min-height:125px;
  border-right:1px solid #E0E0E0;
  border-bottom:1px solid #E0E0E0;
  margin-right:-1px;
}

.date {
  text-align: center;
}

.dayOfWeek {
  flex:1;
  border-right:1px solid #E0E0E0;
  margin-right:-1px;
  text-align:center;
}

.notCurrentMonth {
  background-color: #f7f7f7;
}

.plan {
  color:white;
  margin-bottom:1px;
  height:25px;
  line-height:25px;
  position: relative;
  z-index:1;
  border-radius:4px;
  padding-left:4px;
}

.title {
  text-align: center;
}

div.content {
  position: relative;
}

div.icon-wrap {
  position: absolute;
  padding: 5%;
  right: 0;
  opacity: 0.3;
}

span.error {
  color: red;
}
</style>