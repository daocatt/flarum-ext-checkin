import Component from "flarum/Component";
import app from "flarum/app";
import type dayjs from 'dayjs';
import LoadingIndicator from "flarum/components/LoadingIndicator";
import icon from 'flarum/common/helpers/icon';
import CheckinButton from './CheckinButton';

export default class CheckinContent extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    this.state = this.attrs.state;
  }

  getWeekdays() {
    // for China, Monday/day(1) is first day
    let displayDaysCount = 0;
    let startday;
    // if(app.forum.attribute('checkinConstant') === 1){
    //   displayDaysCount = app.forum.attribute('checkinConstantDays');
    //   startday = dayjs().subtract(displayDaysCount-1, 'day')
    // } else {
    //   startday = dayjs().day(1);
    //   displayDaysCount = 7;
    // }

    let todayNum = dayjs().day();
    if(todayNum === 0){
      todayNum = 7;
    }
    startday = dayjs().subtract(todayNum-1,'day');
    
    displayDaysCount = 7;

    const displayDays = [];
    displayDays.push(startday.format('YYYY-MM-DD'));

    let i = 1;
    while(i <= displayDaysCount){
      displayDays.push(startday.add(i, 'day').format('YYYY-MM-DD'));
      i++;
    }

    return displayDays;
  }

  view() {
    const checkins = this.state.cache || [];

    if(app.session.user){
        const allowCheckin = app.session.user.attribute("allowCheckin");
        const displayDays = this.getWeekdays();
        return (
            <div className="Checkin-page NotificationList CheckinList">
                <div className="NotificationList-header">
                    <h4 className="App-titleControl App-titleControl--text">{app.translator.trans('gtdxyz-checkin.forum.checkin')}</h4>
                </div>
                <div className="NotificationList-content">
                    <ul className="NotificationGroup-content">
                        {checkins.length > 0 ? (
                        checkins.map((checkinItem,indx) => {
                            const check_status = checkinItem.id()>0?'checked':'uncheck';

                            return (
                            <li className={check_status + ' count-'+checkins.length} title={check_status}>
                                
                                    {checkinItem.id() > 0 ? (
                                        <view>
                                        <div className="Notification-content">
                                            <span>
                                            {dayjs(checkinItem.checkin_time()).format('MM/DD')}
                                            </span>
                                            <span>{dayjs(checkinItem.checkin_time()).format('ddd')}</span>
                                        </div>
                                        <div className="Notification-excerpt">
                                            {icon('fas fa-star', { className: 'Notification-icon' })}
                                        </div>
                                        </view>
                                    ) : (
                                    <view>
                                        <div className="Notification-content">
                                        <span>
                                        {dayjs(displayDays[indx]).format('MM/DD')}
                                        </span>
                                        <span>{dayjs(displayDays[indx]).format('ddd')}</span>
                                        </div>
                                        <div className="Notification-excerpt">

                                        {dayjs().isAfter(displayDays[indx],'day') ? (
                                            icon('fas fa-minus', { className: 'Notification-icon' })
                                        ) : (
                                            icon('far fa-star', { className: 'Notification-icon' })
                                        )}

                                        </div>
                                    </view>
                                    )}
                                
                            </li>
                            );

                        })
                        ) : !this.loading ? (
                        <div className="NotificationList-empty">{app.translator.trans('gtdxyz-checkin.forum.empty-text')}</div>
                        ) : (
                        <LoadingIndicator className="LoadingIndicator--block" />
                        )}
                        
                    </ul>
                    <div className="subtitle">
                        {app.translator.trans('gtdxyz-checkin.forum.count-text', {count: app.session.user.attribute('checkin_days_count')})} <br />
                        {
                        app.forum.attribute('checkinConstantForce')===1 && (
                            app.translator.trans('gtdxyz-checkin.forum.constant-recent-count-text', {count: app.session.user.attribute('checkin_constant_count')})
                        )
                        }
                    </div>
                    <div className="Form-group">
                        {allowCheckin ? (
                        <CheckinButton state='enabled' />
                        ) : (
                        <CheckinButton state="disabled" />
                        )}
                    </div>
                </div>

            </div>
        );
    }
  }
}
