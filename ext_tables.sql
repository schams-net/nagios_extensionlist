#
# Table structure for table 'tx_nagiosextensionlist_domain_model_accesshistory'
#
CREATE TABLE tx_nagiosextensionlist_domain_model_accesshistory (
    uid int(11) NOT NULL AUTO_INCREMENT,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    remote_address varchar(250) DEFAULT '' NOT NULL,
    x_forwarded_for varchar(250) DEFAULT '' NOT NULL,
    country_code varchar(250) DEFAULT '' NOT NULL,
    nagios_plugin_version varchar(250) DEFAULT '' NOT NULL,
    nagios_version varchar(250) DEFAULT '' NOT NULL,
    useragent varchar(250) DEFAULT '' NOT NULL,
    request varchar(250) DEFAULT '' NOT NULL,
    PRIMARY KEY (uid)
);
