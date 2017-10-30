DELETE FROM configuration WHERE configuration_key LIKE 'EVENTS_%';
DELETE FROM configuration_group WHERE configuration_group_title = 'Scheduled Events Settings';
DROP TABLE IF EXISTS sched_events;
DROP TABLE IF EXISTS sched_events_content;
DELETE FROM admin_pages WHERE page_key IN ('localizationEventsBox', 'configEventsBox');