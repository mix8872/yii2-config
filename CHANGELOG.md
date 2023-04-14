# Changelog

## 3.0.0

- Integration bootstrap5

## 2.1.0

- update package yiisoft/yii2-symfonymailer to version 3.0.0

## 2.0.1

- Fixed createItem permission bug

## 2.0.0

- Tabs added
- Added tabs table (migration)
- Redesigned editing of options
- Added access rules for options (check by $user->can())

To upgrade module you should run migration:
```
yii migrate --migrationPath="@vendor/mix8872/yii2-config/src/migrations"
```


## 1.1.1

- Fixed some bugs

## 1.1.0

- Add save by ctrl+S
- Change group field widget to kartik select2
- Add date type with datepicker
- Add new migration
- Add update page

## 1.0.0

- Basic functional
