import {extend, override} from 'flarum/extend';

app.initializers.add('gtdxyz-checkin', () => {
  app.extensionData
    .for('gtdxyz-checkin')
    .registerSetting({
      setting: 'gtdxyz-checkin.position',
      label: app.translator.trans('gtdxyz-checkin.admin.settings.position'),
      type: 'select',
      options: {
        0: app.translator.trans('gtdxyz-checkin.admin.settings.sidebar'),
        1: app.translator.trans('gtdxyz-checkin.admin.settings.headbar'),
      },
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-checkin.admin.settings.reward')}</label>
          <div class="helpText">{app.translator.trans('gtdxyz-checkin.admin.settings.require-install')}<code>gtdxyz/flarum-ext-money-plus</code></div>
          <input type="number" className="FormControl" step="any" bidi={this.setting('gtdxyz-checkin.reward')} />
        </div>
      );
    })
    .registerSetting({
      setting: 'gtdxyz-checkin.constant',
      label: app.translator.trans('gtdxyz-checkin.admin.settings.constant'),
      type: 'select',
      options: {
        0: app.translator.trans('gtdxyz-checkin.admin.settings.constant-none'),
        1: app.translator.trans('gtdxyz-checkin.admin.settings.constant-byday'),
        2: app.translator.trans('gtdxyz-checkin.admin.settings.constant-byweekday'),
      },
    })
    .registerSetting({
      setting: 'gtdxyz-checkin.constant-force',
      label: app.translator.trans('gtdxyz-checkin.admin.settings.constant-force'),
      type: 'switch',
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <div class="helpText">{app.translator.trans('gtdxyz-checkin.admin.settings.constant-help')}</div>
        </div>
      );
    })
    .registerSetting({
      setting: 'gtdxyz-checkin.constant-days',
      label: app.translator.trans('gtdxyz-checkin.admin.settings.constant-days'),
      type: 'number',
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-checkin.admin.settings.constant-reward')}</label>
          <div class="helpText">{app.translator.trans('gtdxyz-checkin.admin.settings.require-install')}<code>gtdxyz/flarum-ext-money-plus</code></div>
          <input type="number" className="FormControl" step="any" bidi={this.setting('gtdxyz-checkin.constant-reward')} />
        </div>
      );
    })
    .registerSetting({
      setting: 'gtdxyz-checkin.auto',
      label: app.translator.trans('gtdxyz-checkin.admin.settings.auto'),
      type: 'switch',
    })
    .registerSetting({
      setting: 'gtdxyz-checkin.success-type',
      label: app.translator.trans('gtdxyz-checkin.admin.settings.success-type'),
      type: 'select',
      options: {
        0: app.translator.trans('gtdxyz-checkin.admin.settings.None'),
        1: app.translator.trans('gtdxyz-checkin.admin.settings.Alert'),
        2: app.translator.trans('gtdxyz-checkin.admin.settings.Modal')
      },
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-checkin.admin.settings.success-text')}</label>
          <div class="helpText">{app.translator.trans('gtdxyz-checkin.admin.settings.success-example-text')}</div>
          <input type="string" className="FormControl" step="any" bidi={this.setting('gtdxyz-checkin.success-text')} />
        </div>
      );
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-checkin.admin.settings.success-reward-text')}</label>
          <div class="helpText">{app.translator.trans('gtdxyz-checkin.admin.settings.success-example-reward-text')}</div>
          <input type="string" className="FormControl" step="any" bidi={this.setting('gtdxyz-checkin.success-reward-text')} />
        </div>
      );
    })
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('gtdxyz-checkin.admin.settings.allow-checkin'),
        permission: 'checkin.allowCheckin',
      },
      'moderate',
      90
    )
});
