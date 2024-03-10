import app from 'flarum/forum/app';
import NotificationsDropdown from 'flarum/forum/components/NotificationsDropdown';
import icon from 'flarum/common/helpers/icon';
import type Mithril from 'mithril';
import CheckinList from './CheckinList';

export default class CheckinModal extends NotificationsDropdown {
  static initAttrs(attrs) {
    attrs.label = attrs.label || app.translator.trans('gtdxyz-checkin.forum.checkin');
    attrs.icon = attrs.icon || 'fas fa-calendar-alt';
    attrs.menuClassName = 'forum-checkin';
    super.initAttrs(attrs);
  }

  // getButtonContent(): Mithril.ChildArray {
  //   const unread = this.getUnreadCount();

  //   return [
  //     this.attrs.icon ? <span className="Button-text">{icon(this.attrs.icon, { className: 'Button-icon' })}{this.attrs.label}</span> : null,
  //     unread !== 0 && <span className="NotificationsDropdown-unread">{unread}</span>,
  //     <span className="Button-label">{this.attrs.label}</span>,
  //   ];
  // }

  getMenu() {
    return (
      <div className={'Dropdown-menu ' + this.attrs.menuClassName} onclick={this.menuClick.bind(this)}>
        {this.showing && <CheckinList state={this.attrs.state} />}
      </div>
    );
  }

  

  goToRoute() {
    m.route.set(app.route('UserCheckinRoute', {
      username: app.session.user.username(),
    }));
  }

  getUnreadCount() {
    return 0;
  }

  getNewCount() {
      return 0;
  }
}
