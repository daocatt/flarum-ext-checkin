import Model from 'flarum/common/Model';
import type User from 'flarum/common/models/User';

export default class UserCheckinHistory extends Model {
  // type() {
  //   return Model.attribute<string>('type').call(this);
  // }
  // event_id() {
  //   return Model.attribute<integer | null>('event_id').call(this);
  // }
  // reward_money() {
  //   return Model.attribute<string | null>('reward_money').call(this);
  // }
  // checkin_time() {
  //   return Model.attribute('checkin_time', Model.transformDate).call(this);
  // }
  
  // user() {
  //   return Model.hasOne<User | null>('user').call(this);
  // }
}
Object.assign(UserCheckinHistory.prototype, {
  user_id : Model.attribute('user_id'),
  type : Model.attribute('type'),
  event_id : Model.attribute('event_id'),
  checkin_time : Model.attribute('checkin_time'),
  reward_money : Model.attribute('reward_money'),
  constant: Model.attribute('constant'),
  remark: Model.attribute('remark'),
  user : Model.hasOne('user'),
})
