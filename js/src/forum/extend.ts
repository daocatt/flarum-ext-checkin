import Extend from 'flarum/common/extenders';
import UserCheckinHistory from './models/UserCheckinHistory';

export default [
  
  new Extend.Store() //
    .add('checkins', UserCheckinHistory),

];
