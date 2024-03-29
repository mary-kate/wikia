<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * @file
 * @ingroup Extensions
 */

/**
 * Array mapping all editable settings to their type depending of their section
 * First two keys will be used to show sections
 * These settings are more or less in the same order as
 * http://www.mediawiki.org/wiki/Manual:Configuration_settings
 */
$settings = array(
	'site' => array(
		'site' => array(
			'wgSitename' => 'text',
			'wgLogo' => 'image-url',
			'wgBreakFrames' => 'bool',
			'wgCapitalLinks' => 'bool',
			'wgDefaultLanguageVariant' => 'text',
			'wgExtraRandompageSQL' => 'text',
			'wgExtraSubtitle' => 'text',
			'wgLegalTitleChars' => 'text',
			'wgMaxArticleSize' => 'int',
			'wgMetadataWhitelist' => 'text',
			'wgSiteNotice' => 'text',
			'wgSiteSupportPage' => 'text',
			'wgStyleVersion' => 'int',
			'wgUrlProtocols' => 'array',
			'wgUseMetadataEdit' => 'bool',
		),
		'robots' => array(
			'wgArticleRobotPolicies' => 'array',
			'wgDefaultRobotPolicy' => 'text',
			'wgExemptFromUserRobotsControl' => 'array',
			'wgNoFollowLinks' => 'bool',
		),
	),
	'features' => array(
		'features' => array(
			'wgUseExternalEditor' => 'bool',
			'wgUniversalEditButton' => 'bool',
			'wgPageShowWatchingUsers' => 'bool',
			'wgEdititis' => 'bool',
			'wgShowHostnames' => 'bool',
			'wgUseDumbLinkUpdate' => 'bool',
			'wgFixDoubleRedirects' => 'bool',
			'wgUsePathInfo' => 'bool',
			'wgEnableAPI' => 'bool',
			'wgEnableWriteAPI' => 'bool',
			'wgUseTeX' => 'bool',
			'wgEnableHtmlDiff' => 'bool',
			'wgUseAutomaticEditSummaries' => 'bool',
			'wgUseTrackbacks' => 'bool',
		),
		'ajax' => array(
			'wgUseAjax' => 'bool',
			'wgAjaxExportList' => 'array',
			'wgAjaxUploadDestCheck' => 'bool',
			'wgAjaxWatch' => 'bool',
			'wgLivePreview' => 'bool',
		),
		'css-js' => array(
			'wgAllowUserCss' => 'bool',
			'wgAllowUserJs' => 'bool',
			'wgUseSiteCss' => 'bool',
			'wgUseSiteJs' => 'bool',
			'wgHandheldStyle' => 'text',
			'wgHandheldForIPhone' => 'bool',
		),
		'performance' => array(
			'wgAPIMaxDBRows' => 'int',
			'wgDisableQueryPages' => 'bool',
			'wgDisableQueryPageUpdate' => 'array',
			'wgDisableSearchContext' => 'bool',
			'wgDisableSearchUpdate' => 'bool',
			'wgDisableTextSearch' => 'bool',
			'wgMaximumMovedPages' => 'int',
			'wgMiserMode' => 'bool',
			'wgCompressRevisions' => 'bool',
			'wgUpdateRowsPerQuery' => 'int',
		),
	),
	'namespaces' => array(
		'namespaces' => array(
			'wgContentNamespaces' => 'array',
			'wgExtraNamespaces' => 'array',
			'wgMetaNamespace' => 'text',
			'wgMetaNamespaceTalk' => 'text',
			'wgNamespaceAliases' => 'array',
			'wgNamespaceProtection' => 'array',
			'wgNamespaceRobotPolicies' => 'array',
			'wgNamespacesToBeSearchedDefault' => 'array',
			'wgNamespacesToBeSearchedProject' => 'array',
			'wgNamespacesWithSubpages' => 'array',
			'wgNoFollowNsExceptions' => 'array',
			'wgNonincludableNamespaces' => 'array',
			'wgSitemapNamespaces' => 'array',
		),
	),
	'groups' => array(
		'groups' => array(
			'wgAutopromote' => 'array',
			'wgGroupPermissions' => 'array',
			'wgAddGroups' => 'array',
			'wgRemoveGroups' => 'array',
			'wgGroupsAddToSelf' => 'array',
			'wgGroupsRemoveFromSelf' => 'array',
		),
	),
	'paths' => array(
		'paths' => array(
			'wgActionPaths' => 'array',
			'wgAppleTouchIcon' => 'text',
			'wgArticlePath' => 'text',
			'wgFavicon' => 'text',
			'wgMathDirectory' => 'text',
			'wgMathPath' => 'text',
			'wgRedirectScript' => 'text',
			'wgProto' => 'text',
			'wgScript' => 'text',
			'wgScriptExtension' => 'text',
			'wgScriptPath' => 'text',
			'wgServer' => 'text',
			'wgServerName' => 'text',
			'wgStyleDirectory' => 'text',
			'wgStylePath' => 'text',
			'wgStyleSheetPath' => 'text',
			'wgTmpDirectory' => 'text',
			'wgUploadBaseUrl' => 'text',
			'wgUploadDirectory' => 'text',
			'wgUploadNavigationUrl' => 'text',
			'wgUploadPath' => 'text',
			'wgVariantArticlePath' => 'text',
		),
		'external-tools' => array(
			'wgDiff' => 'text',
			'wgDiff3' => 'text',
			'wgPhpCli' => 'text',
			'wgTexvc' => 'text',
			'wgExternalDiffEngine' => 'text',
		),
		'filesystem' => array(
			'wgDirectoryMode' => 'int',
			'wgSQLiteDataDirMode' => 'int',
			'wgMaxShellFileSize' => 'int',
			'wgMaxShellMemory' => 'int',
			'wgMaxShellTime' => 'int',
			'wgShellLocale' => 'text',
		),
	),
	'db' => array(
		'db' => array(
			'wgAllDBsAreLocalhost' => 'bool',
			'wgCheckDBSchema' => 'bool',
			'wgDBAvgStatusPoll' => 'int',
#			'wgDBconnection' => 'text', # This setting is deprecated and has an uncertain history. Best not to let people change it.
			'wgDBerrorLog' => 'text',
			'wgDBname' => 'text',
			'wgDBpassword' => 'text',
			'wgDBport' => 'int',
			'wgDBserver' => 'text',
			'wgDBtype' => array( 'mysql' => 'MySQL', 'postgres' => 'PostreSQL' ),
			'wgDBuser' => 'text',
#			'wgLegacySchemaConversion' => 'bool', # Really, really deprecated.
			'wgLocalDatabases' => 'array',
			'wgSearchType' => 'text',
			'wgSharedDB' => 'text',
			'wgSharedPrefix' => 'text',
			'wgSharedTables'  => 'array',
			'wgAntiLockFlags' => 'int',
		),
		'load-balancing' => array(
			'wgDBClusterTimeout' => 'int',
			'wgDBservers' => 'array',
			'wgDefaultExternalStore' => 'array',
			'wgExternalServers' => 'array',
			'wgLBFactoryConf' => 'array',
			'wgMasterWaitTimeout' => 'int',
			'wgSlaveLagCritical' => 'int',
			'wgSlaveLagWarning' => 'int',
		),
		'mysql' => array(
			'wgDBmysql4' => 'bool',
			'wgDBmysql5' => 'bool',
			'wgDBprefix' => 'text',
			'wgDBTableOptions' => 'text',
			'wgDBtransactions' => 'bool',
		),
		'postgres' => array(
			'wgDBmwschema' => 'text',
			'wgDBts2schema' => 'text',
		),
		'sqlite' => array(
			'wgSQLiteDataDir' => 'text',
		),
	),
	'email' => array(
		'email' => array(
			'wgEmailAuthentication' => 'bool',
			'wgEmergencyContact' => 'text',
			'wgEnableEmail' => 'bool',
			'wgEnableUserEmail' => 'bool',
			'wgNoReplyAddress' => 'text',
			'wgPasswordSender' => 'text',
			'wgPasswordReminderResendTime' => 'int',
			'wgSMTP' => 'array',
			'wgUserEmailUseReplyTo' => 'bool',
		),
		'enotif' => array(
			'wgEnotifFromEditor' => 'bool',
			'wgEnotifImpersonal' => 'bool',
			'wgEnotifMaxRecips' => 'int',
			'wgEnotifMinorEdits' => 'bool',
			'wgEnotifRevealEditorAddress' => 'bool',
			'wgEnotifUseJobQ' => 'bool',
			'wgEnotifUseRealName' => 'bool',
			'wgEnotifUserTalk' => 'bool',
			'wgEnotifWatchlist' => 'bool',
			'wgUsersNotifiedOnAllChanges' => 'array',
		),
	),
	'localization' => array(
		'localization' => array(
			'wgExtraLanguageNames' => 'array',
			'wgAmericanDates' => 'bool',
			'wgDisableLangConversion' => 'bool',
			'wgDisableTitleConversion' => 'bool',
			'wgEditEncoding' => 'text',
			'wgForceUIMsgAsContentMsg' => 'array',
			'wgInputEncoding' => 'text',
			'wgLanguageCode' => 'lang',
			'wgLegacyEncoding' => 'text',
			'wgLoginLanguageSelector' => 'bool',
			'wgOutputEncoding' => 'text',
			'wgTranslateNumerals' => 'bool',
			'wgUseDatabaseMessages' => 'bool',
			'wgUseDynamicDates' => 'bool',
		),
		'timezone' => array(
			'wgLocaltimezone' => 'text',
			'wgLocalTZoffset' => 'int',
		),
		'zh-conversion' => array(
			'wgUseZhdaemon' => 'bool',
			'wgZhdaemonHost' => 'text',
			'wgZhdaemonPort' => 'int',
		),
	),
	'output' => array(
		'output' => array(
			'wgDocType' => 'text',
			'wgDTD' => 'text',
			'wgMimeType' => 'text',
			'wgXhtmlDefaultNamespace' => 'text',
			'wgXhtmlNamespaces' => 'array',
			'wgDisableOutputCompression' => 'bool',
		),
	),
	'debug' => array(
		'debug' => array(
			'wgColorErrors' => 'bool',
			'wgDebugComments' => 'bool',
			'wgDebugDumpSql' => 'bool',
			'wgDebugLogFile' => 'text',
			'wgDebugLogGroups' => 'array',
			'wgDebugRawPage' => 'bool',
			'wgDebugLogPrefix' => 'text',
			'wgDebugRedirects' => 'bool',
			'wgLogQueries' => 'bool',
			'wgShowExceptionDetails' => 'bool',
			'wgShowSQLErrors' => 'bool',
			'wgStatsMethod' => array( 'cache' => 'Cache', 'udp' => 'UDP', 0 => 'None' ),
		),
		'profiling' => array(
			'wgDebugFunctionEntry' => 'bool',
			'wgDebugProfiling' => 'bool',
			'wgDebugSquid' => 'bool',
			'wgProfileCallTree' => 'bool',
			'wgProfileLimit' => 'int',
			'wgProfileOnly' => 'bool',
			'wgProfilePerHost' => 'bool',
			'wgProfileToDatabase' => 'bool',
			'wgUDPProfilerHost' => 'text',
			'wgUDPProfilerPort' => 'int',
		),
	),
	'stats' => array(
		'stats' => array(
			'wgCountCategorizedImagesAsUsed' => 'bool',
			'wgDisableCounters' => 'bool',
			'wgHitcounterUpdateFreq' => 'int',
			'wgUseCommaCount' => 'bool',
			'wgWantedPagesThreshold' => 'int',
		),
	),
	'skin' => array(
		'skin' => array(
			'wgDefaultSkin' => 'text',
			'wgSkipSkin' => 'text',
			'wgSkipSkins' => 'array',
		),
	),
	'category' => array(
		'category' => array(
			'wgCategoryMagicGallery' => 'bool',
			'wgCategoryPagingLimit' => 'int',
			'wgCategoryPrefixedDefaultSortkey' => 'bool',
			'wgUseCategoryBrowser' => 'bool',
		),
	),
	'cache' => array(
		'cache' => array(
			'wgMainCacheType' => array( -1 => 'Anything', 0 => 'None',
	                            1 => 'DB', 2 => 'Memcached',
	                            3 => 'Accel', 4 => 'DBA' ),
	        	'wgCacheEpoch' => 'text',
			'wgCachePages' => 'bool',
			'wgClockSkewFudge' => 'int',
			'wgFileCacheDirectory' => 'text',
			'wgForcedRawSMaxage' => 'int',
			'wgQueryCacheLimit' => 'int',
			'wgRevisionCacheExpiry' => 'int',
			'wgTranscludeCacheExpiry' => 'int',
			'wgUseFileCache' => 'bool',
			'wgUseGzip' => 'bool',
		),
		'pcache' => array(
			'wgParserCacheType' => array( -1 => 'Anything', 0 => 'None',
			                              1 => 'DB', 2 => 'Memcached',
			                              3 => 'Accel', 4 => 'DBA' ),
			'wgEnableParserCache' => 'bool',
			'wgEnableSidebarCache' => 'bool',
			'wgRenderHashAppend' => 'text',
			'wgSidebarCacheExpiry' => 'int',
			'wgUseETag' => 'bool',
		),
		'messagecache' => array(
			'wgMessageCacheType' => array( -1 => 'Anything', 0 => 'None',
			                               1 => 'DB', 2 => 'Memcached',
			                               3 => 'Accel', 4 => 'DBA' ),
			'wgEnableSerializedMessages' => 'bool',
			'wgLocalMessageCache' => 'text',
			'wgMsgCacheExpiry' => 'int',
			'wgCheckSerialized' => 'bool',
			'wgLocalMessageCacheSerialized' => 'bool',
			'wgMaxMsgCacheEntrySize' => 'int',
		),
		'memcached' => array(
#			'wgMemCachedDebug' => 'bool', # Does not appear to be a setting, just a global
			'wgMemCachedPersistent' => 'bool',
			'wgMemCachedServers' => 'array',
			'wgSessionsInMemcached' => 'bool',
			'wgUseMemCached' => 'bool',
		),
	),
	'interwiki' => array(
		'interwiki' => array(
			'wgHideInterlanguageLinks' => 'bool',
			'wgInterwikiMagic' => 'bool',
			'wgDisableHardRedirects' => 'bool',
			'wgEnableScaryTranscluding' => 'bool',
			'wgImportSources' => 'array',
			'wgInterwikiCache' => 'text',
			'wgInterwikiExpiry' => 'int',
			'wgInterwikiFallbackSite' => 'text',
			'wgInterwikiScopes' => 'int',
			'wgLocalInterwiki' => 'text',
			'wgRedirectSources' => 'text',
		),
	),
	'access' => array(
		'access' => array(
			'wgAccountCreationThrottle' => 'int',
			'wgAllowPageInfo' => 'bool',
			'wgDisabledActions' => 'array',
			'wgEmailConfirmToEdit' => 'bool',
			'wgImplicitGroups' => 'array',
			'wgPasswordSalt' => 'bool',
			'wgReadOnly' => 'text',
			'wgReadOnlyFile' => 'text',
			'wgRestrictionTypes' => 'array',
			'wgRestrictionLevels' => 'array',
			'wgWhitelistRead' => 'array',
		),
		'block' => array(
			'wgBlockAllowsUTEdit' => 'bool',
			'wgSysopEmailBans' => 'bool',
			'wgSysopRangeBans' => 'bool',
			'wgSysopUserBans' => 'bool',
			'wgAutoblockExpiry' => 'int',
		),
		'filter' => array(
			'wgSpamRegex' => 'array',
			'wgFilterCallback' => 'text',
			'wgDeleteRevisionsLimit' => 'int',
			'wgRateLimitLog' => 'text',
			'wgRateLimits' => 'array',
			'wgRateLimitsExcludedGroups' => 'array',
			'wgPasswordAttemptThrottle' => 'array',
		),
	),
	'proxy' => array(
		'proxy' => array(
			'wgBlockOpenProxies' => 'bool',
			'wgEnableSorbs' => 'bool',
#			'wgProxyKey' => 'text', # Deprecated
			'wgProxyList' => 'array',
			'wgProxyMemcExpiry' => 'int',
			'wgProxyPorts' => 'array',
			'wgProxyScriptPath' => 'text',
			'wgProxyWhitelist' => 'array',
			'wgSecretKey' => 'text',
			'wgSorbsUrl' => 'text',
		),
	),
	'squid' => array(
		'squid' => array(
			'wgUseSquid' => 'bool',
			'wgInternalServer' => 'text',
			'wgMaxSquidPurgeTitles' => 'int',
			'wgSquidMaxage' => 'int',
			'wgSquidServers' => 'array',
			'wgSquidServersNoPurge' => 'array',
			'wgUseESI' => 'bool',
			'wgUsePrivateIPs' => 'bool',
		),
		'htcp' => array(
			'wgHTCPMulticastAddress' => 'text',
			'wgHTCPMulticastTTL' => 'int',
			'wgHTCPPort' => 'int',
		),
	),
	'cookie' => array(
		'cookie' => array(
			'wgCacheVaryCookies' => 'array',
			'wgCookieDomain' => 'text',
			'wgCookieExpiration' => 'int',
			'wgCookieHttpOnly' => 'bool',
			'wgCookiePath' => 'text',
			'wgCookiePrefix' => 'text',
			'wgCookieSecure' => 'bool',
			'wgDisableCookieCheck' => 'bool',
			'wgHttpOnlyBlacklist' => 'array',
			'wgSessionName' => 'text',
		),
	),
	'upload' => array(
		'upload' => array(
			'wgAjaxLicensePreview' => 'bool',
			'wgAllowCopyUploads' => 'bool',
			'wgCheckFileExtensions' => 'bool',
			'wgEnableUploads' => 'bool',
			'wgFileBlacklist' => 'array',
			'wgFileExtensions' => 'array',
			'wgFileStore' => 'array',
			'wgHashedUploadDirectory' => 'bool',
			'wgLocalFileRepo' => 'array',
			'wgRemoteUploads' => 'bool',
			'wgStrictFileExtensions' => 'bool',
			'wgUploadSizeWarning' => 'int',
			'wgMaxUploadSize' => 'int',
			'wgHTTPTimeout' => 'int',
			'wgHTTPProxy' => 'text',
			'wgXMLMimeTypes' => 'array',
		),
		'sharedupload' => array(
			'wgCacheSharedUploads' => 'bool',
			'wgForeignFileRepos' => 'array',
			'wgFetchCommonsDescriptions' => 'bool',
			'wgHashedSharedUploadDirectory' => 'bool',
			'wgRepositoryBaseUrl' => 'text',
			'wgSharedThumbnailScriptPath' => 'text',
			'wgSharedUploadDBname' => 'text',
			'wgSharedUploadDBprefix' => 'text',
			'wgSharedUploadDirectory' => 'text',
			'wgSharedUploadPath' => 'text',
			'wgUseSharedUploads' => 'bool',
		),
		'mime' => array(
			'wgJsMimeType' => 'text',
			'wgLoadFileinfoExtension' => 'bool',
			'wgMimeDetectorCommand' => 'text',
			'wgMimeInfoFile' => 'text',
			'wgMimeTypeFile' => 'text',
			'wgTrivialMimeDetection' => 'bool',
			'wgVerifyMimeType' => 'bool',
			'wgMimeTypeBlacklist' => 'array',
		),
	),
	'images' => array(
		'images' => array(
			'wgAllowImageMoving' => 'bool',
			'wgCustomConvertCommand' => 'text',
			'wgIgnoreImageErrors' => 'bool',
			'wgImageLimits' => 'array',
			'wgMaxAnimatedGifArea' => 'int',
			'wgMaxImageArea' => 'int',
			'wgMediaHandlers' => 'array',
			'wgShowEXIF' => 'bool',
			'wgTrustedMediaFormats' => 'array',
		),
		'thumbnail' => array(
			'wgThumbnailEpoch' => 'text',
			'wgThumbnailScriptPath' => 'text',
			'wgThumbUpright' => 'text',
			'wgGenerateThumbnailOnParse' => 'bool',
			'wgThumbLimits' => 'array',
		),
		'djvu' => array(
			'wgDjvuDump' => 'text',
			'wgDjvuOutputExtension' => 'text',
			'wgDjvuPostProcessor' => 'text',
			'wgDjvuRenderer' => 'text',
			'wgDjvuToXML' => 'text',
		),
		'imagemagick' => array(
			'wgImageMagickConvertCommand' => 'text',
			'wgImageMagickTempDir' => 'text',
			'wgSharpenParameter' => 'int',
			'wgSharpenReductionThreshold' => 'text',
			'wgUseImageMagick' => 'bool',
		),
		'svg' => array(
			'wgAllowTitlesInSVG' => 'bool',
			'wgSVGConverter' => 'text',
			'wgSVGConverterPath' => 'text',
			'wgSVGConverters' => 'array',
			'wgSVGMaxSize' => 'int',
		),
		'antivirus' => array(
			'wgAntivirus' => 'text',
			'wgAntivirusRequired' => 'bool',
			'wgAntivirusSetup' => 'array',
		),
	),
	'parser' => array(
		'parser' => array(
			'wgAllowDisplayTitle' => 'bool',
			'wgAllowExternalImages' => 'bool',
			'wgAllowExternalImagesFrom' => 'text',
			'wgEnableImageWhitelist' => 'bool',
			'wgExpensiveParserFunctionLimit' => 'int',
			'wgExternalLinkTarget' => 'text',
			'wgCleanSignatures' => 'bool',
			'wgGrammarForms' => 'array',
			'wgLinkHolderBatchSize' => 'int',
			'wgMaxPPExpandDepth' => 'int',
			'wgMaxPPNodeCount' => 'int',
			'wgMaxTemplateDepth' => 'int',
			'wgMaxTocLevel' => 'int',
			'wgParserConf' => 'array',
			'wgParserCacheExpireTime' => 'int',
			'wgParserTestFiles' => 'array',
			'wgRestrictDisplayTitle' => 'bool',
		),
		'html' => array(
			'wgRawHtml' => 'bool',
		),
		'tidy' => array(
			'wgAlwaysUseTidy' => 'bool',
			'wgDebugTidy' => 'bool',
			'wgTidyBin' => 'text',
			'wgTidyConf' => 'text',
			'wgTidyInternal' => 'bool',
			'wgTidyOpts' => 'text',
			'wgUseTidy' => 'bool',
			'wgValidateAllHtml' => 'bool',
		),
	),
	'specialpages' => array(
		'specialpages' => array(
			'wgAllowSpecialInclusion' => 'bool',
			'wgExportAllowHistory' => 'bool',
			'wgExportAllowListContributors' => 'bool',
			'wgExportMaxHistory' => 'int',
			'wgFilterLogTypes' => 'array',
			'wgImportTargetNamespace' => 'text',
			'wgLogActions' => 'array',
			'wgLogActionsHandlers' => 'array',
			'wgLogHeaders' => 'array',
			'wgLogNames' => 'array',
			'wgLogRestrictions' => 'array',
			'wgLogTypes' => 'array',
			'wgMaxRedirectLinksRetrieved' => 'int',
			'wgSortSpecialPages' => 'bool',
			'wgSpecialPageGroups' => 'array',
			'wgSpecialVersionShowHooks' => 'bool',
			'wgUseNPPatrol' => 'bool',
		),
		'recentchanges' => array(
			'wgAllowCategorizedRecentChanges' => 'bool',
			'wgPutIPinRC' => 'bool',
			'wgRCChangedSizeThreshold' => 'int',
			'wgRCFilterByAge' => 'bool',
			'wgRCLinkLimits' => 'array',
			'wgRCLinkDays' => 'array',
			'wgRCMaxAge' => 'int',
			'wgRCShowChangedSize' => 'bool',
			'wgRCShowWatchingUsers' => 'bool',
			'wgShowUpdatedMarker' => 'bool',
			'wgUseRCPatrol' => 'bool',
			'wgRC2UDPAddress' => 'text',
			'wgRC2UDPInterwikiPrefix' => 'bool',
			'wgRC2UDPOmitBots' => 'bool',
			'wgRC2UDPPort' => 'int',
			'wgRC2UDPPrefix' => 'text',
		),
	),
	'users' => array(
		'users' => array(
			'wgActiveUserDays' => 'int',
			'wgActiveUserEditCount' => 'int',
			'wgAutoConfirmAge' => 'int',
			'wgAutoConfirmCount' => 'int',
			'wgAllowRealName' => 'bool',
			'wgAllowUserSkin' => 'bool',
			'wgDefaultUserOptions' => 'array',
			'wgDisableAnonTalk' => 'bool',
			'wgMaxNameChars' => 'int',
			'wgMaxSigChars' => 'int',
			'wgMinimalPasswordLength' => 'int',
			'wgNewUserLog' => 'bool',
			'wgReservedUsernames' => 'array',
			'wgShowIPinHeader' => 'bool',
			'wgBrowserBlackList' => 'array',
		),
	),
	'feed' => array(
		'feed' => array(
			'wgFeed' => 'bool',
			'wgFeedCacheTimeout' => 'int',
			'wgFeedDiffCutoff' => 'int',
			'wgFeedLimit' => 'int',
			'wgOverrideSiteFeed' => 'array',
		),
	),
	'copyright' => array(
		'copyright' => array(
			'wgCheckCopyrightUpload' => 'bool',
			'wgCopyrightIcon' => 'text',
			'wgEnableCreativeCommonsRdf' => 'bool',
			'wgEnableDublinCoreRdf' => 'bool',
			'wgMaxCredits' => 'int',
			'wgRightsIcon' => 'text',
			'wgRightsPage' => 'text',
			'wgRightsText' => 'text',
			'wgRightsUrl' => 'text',
			'wgShowCreditsIfMax' => 'bool',
			'wgUseCopyrightUpload' => 'bool',
		),
	),
	'job' => array(
		'job' => array(
			'wgJobRunRate' => 'int',
			'wgJobClasses' => 'array',
			'wgUpdateRowsPerJob' => 'int',
		),
	),
	'extension' => array(
		'extension' => array(
			'wgAllowSlowParserFunctions' => 'bool',
			'wgAPIListModules' => 'array',
			'wgAPIMetaModules' => 'array',
			'wgAPIModules' => 'array',
			'wgAPIPropModules' => 'array',
			'wgAutoloadClasses' => 'array',
			'wgAvailableRights' => 'array',
			'wgDisableInternalSearch' => 'bool',
			'wgExceptionHooks' => 'array',
			'wgExtensionAliasesFiles' => 'array',
			'wgExtensionCredits' => 'array',
			'wgExtensionFunctions' => 'array',
			'wgExtensionMessagesFiles' => 'array',
			'wgExternalStores' => 'array',
			'wgHooks' => 'array',
			'wgPagePropLinkInvalidations' => 'array',
			'wgParserOutputHooks' => 'array',
			'wgSpecialPageCacheUpdates' => 'array',
			'wgSpecialPages' => 'array',
			'wgSkinExtensionFunctions' => 'array',
		),
	),
	'search' => array(
		'search' => array(
			'wgAdvancedSearchHighlighting' => 'bool',
			'wgEnableMWSuggest' => 'bool',
			'wgGoToEdit' => 'bool',
			'wgMWSuggestTemplate' => 'text',
			'wgOpenSearchTemplate' => 'text',
			'wgSearchForwardUrl' => 'text',
			'wgSearchHighlightBoundaries' => 'text',
		),
	),
);

/**
 * As there can be a lot of array types, try to define their type
 *
 * Types used:
 * - simple: single dimension array with numeric key
 * - assoc:  single dimension array with associative key => val
 * - ns-bool:   single dimension array with namespaces numbers in the key and a
 *              boolean value
 * - ns-text:   same as ns-bool but with a string in the value
 * - ns-simple: like simple, but values are restricted to namespaces index
 * - group-bool: two dimensions array with group name in first key, right name
 *               in the second and boolean value
 * - group-array: two dimensions array with group name in first key and then
 *                a 'simple' array
 * - array: other types of arrays not currenty supported
 */
$arrayDefs = array(
	'wgActionPaths' => 'assoc',
# Db
	'wgDBservers' => 'array',
	'wgDefaultExternalStore' => 'simple',
	'wgLocalDatabases' => 'simple',
	'wgLBFactoryConf' => 'array',
	'wgExternalServers' => 'array',
	'wgSharedTables'  => 'simple',
# Email
	'wgSMTP' => 'assoc',
	'wgUsersNotifiedOnAllChanges' => 'simple',
# Localization
	'wgForceUIMsgAsContentMsg' => 'simple',
	'wgXMLMimeTypes' => 'assoc',
	'wgXhtmlNamespaces' => 'assoc',
	'wgDebugLogGroups' => 'assoc',
# Site custom
	'wgAjaxExportList' => 'simple',
	'wgDefaultUserOptions' => 'assoc',
	'wgExtraLanguageNames' => 'assoc',
	'wgRestrictionLevels' => 'simple',
	'wgUrlProtocols' => 'simple',
	'wgContentNamespaces' => 'ns-simple',
	'wgExemptFromUserRobotsControl' => 'ns-simple',
	'wgExtraNamespaces' => 'assoc',
	'wgNamespaceAliases' => 'assoc',
	'wgNamespaceProtection' => 'ns-array',
	'wgNamespaceRobotPolicies' => 'ns-text',
	'wgNamespacesToBeSearchedDefault' => 'ns-bool',
	'wgNamespacesToBeSearchedProject' => 'ns-bool',
	'wgNamespacesWithSubpages' => 'ns-bool',
	'wgNoFollowNsExceptions' => 'ns-simple',
	'wgNonincludableNamespaces' => 'ns-simple',
	'wgSitemapNamespaces' => 'ns-simple',
	'wgArticleRobotPolicies' => 'assoc',
# Skins
	'wgSkipSkins' => 'simple',
# Cache
	'wgMemCachedServers' => 'simple',
# Interwiki
	'wgImportSources' => 'simple',
# Access
	'wgAutopromote' => 'promotion-conds',
	'wgAddGroups' => 'group-array',
	'wgDisabledActions' => 'simple',
	'wgGroupPermissions' => 'group-bool',
	'wgGroupsAddToSelf' => 'group-array',
	'wgGroupsRemoveFromSelf' => 'group-array',
	'wgImplicitGroups' => 'simple',
	'wgPasswordAttemptThrottle' => 'assoc',
	'wgRemoveGroups' => 'group-array',
	'wgWhitelistRead' => 'simple',
	'wgSpamRegex' => 'simple',
# Rate limits
	'wgRateLimits' => 'rate-limits',
	'wgRateLimitsExcludedGroups' => 'simple',
# Proxies
	'wgProxyList' => 'simple',
	'wgProxyPorts' => 'simple',
	'wgProxyWhitelist' => 'simple',
# Squid
	'wgSquidServers' => 'simple',
	'wgSquidServersNoPurge' => 'simple',
# Cookie
	'wgCacheVaryCookies' => 'simple',
	'wgHttpOnlyBlacklist' => 'simple',
# Reduction
	'wgDisableQueryPageUpdate' => 'simple',
# Uploads
	'wgFileBlacklist' => 'simple',
	'wgFileExtensions' => 'simple',
	'wgFileStore' => 'array',
	'wgLocalFileRepo' => 'assoc',
	'wgForeignFileRepos' => 'array',
	'wgMimeTypeBlacklist' => 'simple',
# Images
	'wgImageLimits' => 'simple-dual',
	'wgMediaHandlers' => 'assoc',
	'wgSVGConverters' => 'assoc',
	'wgThumbLimits' => 'simple',
	'wgTrustedMediaFormats' => 'simple',
	'wgAntivirusSetup' => 'array',
# Parser
	'wgParserConf' => 'assoc',
	'wgParserTestFiles' => 'simple',
# Users
	'wgReservedUsernames' => 'simple',
	'wgBrowserBlackList' => 'simple',
# Feed
	'wgOverrideSiteFeed' => 'assoc',
# Special pages
	'wgFilterLogTypes' => 'assoc',
	'wgLogActions' => 'assoc',
	'wgLogActionsHandlers' => 'assoc',
	'wgLogHeaders' => 'assoc',
	'wgLogNames' => 'assoc',
	'wgLogRestrictions' => 'assoc',
	'wgLogTypes' => 'simple',
	'wgSpecialPageGroups' => 'assoc',
	'wgRCLinkLimits' => 'simple',
	'wgRCLinkDays' => 'simple',
# Extensions
	'wgAPIListModules' => 'assoc',
	'wgAPIMetaModules' => 'assoc',
	'wgAPIModules' => 'assoc',
	'wgAPIPropModules' => 'assoc',
	'wgAvailableRights' => 'simple',
	'wgExceptionHooks' => 'array',
	'wgExtensionAliasesFiles' => 'array',
	'wgExtensionCredits' => 'array',
	'wgExtensionFunctions' => 'simple',
	'wgExtensionMessagesFiles' => 'assoc',
	'wgExternalStores' => 'simple',
	'wgSpecialPageCacheUpdates' => 'array',
	'wgSpecialPages' => 'assoc',
# Misc
	'wgGrammarForms' => 'array',
	'wgRestrictionTypes' => 'simple',
);

/**
 * Value to be used when setting matches empty()
 */
$emptyValues = array(
	'wgAppleTouchIcon' => false,
	'wgVariantArticlePath' => false,
	'wgDBerrorLog' => null,
	'wgSearchType' => null,
	'wgSharedDB' => null,
	'wgLegacyEncoding' => null,
	'wgDefaultLanguageVariant' => false,
	'wgExemptFromUserRobotsControl' => null,
	'wgExtraRandompageSQL' => false,
	'wgHandheldStyle' => false,
	'wgMetaNamespaceTalk' => false,
	'wgLocalMessageCache' => null,
	'wgInterwikiCache' => false,
	'wgReadOnly' => null,
	'wgRateLimitLog' => null,
	'wgSessionName' => false,
	'wgHTTPProxy' => false,
	'wgSharedThumbnailScriptPath' => false,
	'wgSharedUploadDBname' => false,
	'wgMimeDetectorCommand' => null,
	'wgCustomConvertCommand' => false,
	'wgThumbnailScriptPath' => false,
	'wgDjvuDump' => null,
	'wgDjvuRenderer' => null,
	'wgDjvuToXML' => null,
	'wgAntivirus' => null,
	'wgExternalLinkTarget' => false,
	'wgImportTargetNamespace' => null,
	'wgRC2UDPAddress' => false,
	'wgRC2UDPPort' => false,
	'wgRightsUrl' => null,
	'wgMWSuggestTemplate' => false,
	'wgOpenSearchTemplate' => false,
	'wgSearchForwardUrl' => null,
	'wgHTCPMulticastAddress' => false,
	'wgExternalDiffEngine' => false,
	'wgSearchType' => null,
	'wgLocaltimezone' => null,
	'wgLocalTZoffset' => null,
	'wgReadOnly' => null,
	'wgDjvuDump' => null,
	'wgAntivirus' => null,
	'wgImportTargetNamespace' => null,
	'wgCopyrightIcon' => null,
	'wgSearchForwardUrl' => null,
	'wgExemptFromUserRobotsControl' => null,
	'wgArticlePath' => false,

);

/**
 * Settings that can be modified only by users with 'configure-all' right
 */
$editRestricted = array(
# General
	'wgActionPaths',
	'wgAppleTouchIcon',
	'wgArticlePath',
	'wgDirectoryMode',
	'wgDiff',
	'wgDiff3',
	'wgFavicon',
	'wgMathDirectory',
	'wgMathPath',
	'wgProto',
	'wgRedirectScript',
	'wgScript',
	'wgScriptExtension',
	'wgScriptPath',
	'wgServer',
	'wgServerName',
	'wgStyleDirectory',
	'wgStylePath',
	'wgStyleSheetPath',
	'wgTmpDirectory',
	'wgUsePathInfo',
	'wgUploadBaseUrl',
	'wgUploadDirectory',
	'wgUploadNavigationUrl',
	'wgUploadPath',
	'wgVariantArticlePath',
# Db
	'wgAllDBsAreLocalhost',
	'wgCheckDBSchema',
	'wgDBAvgStatusPoll',
	'wgDBClusterTimeout',
	'wgDBerrorLog',
	'wgDBmwschema',
	'wgDBmysql5',
	'wgDBname',
	'wgDBpassword',
	'wgDBport',
	'wgDBprefix',
	'wgDBserver',
	'wgDBservers',
	'wgDBTableOptions',
	'wgDBtransactions',
	'wgDBts2schema',
	'wgDBtype',
	'wgDBuser',
	'wgDefaultExternalStore',
	'wgLBFactoryConf',
#	'wgLegacySchemaConversion',
	'wgLocalDatabases',
	'wgMasterWaitTimeout',
	'wgSearchType',
	'wgSharedDB',
	'wgSharedPrefix',
	'wgSharedTables',
	'wgSlaveLagCritical',
	'wgSlaveLagWarning',
	'wgSQLiteDataDir',
	'wgExternalServers',
# Emal
	'wgSMTP',
# Debug
	'wgDebugLogFile',
	'wgDebugLogGroups',
	'wgUDPProfilerHost',
	'wgUDPProfilerPort',
# Cache
	'wgFileCacheDirectory',
	'wgLocalMessageCache',
	'wgMemCachedServers',
# Access
	'wgAddGroups',
	'wgGroupPermissions',
	'wgGroupsAddToSelf',
	'wgGroupsRemoveFromSelf',
	'wgRemoveGroups',
	'wgReadOnlyFile',
# Rate limits
	'wgRateLimitLog',
# Proxies
#	'wgProxyKey', # Deprecated
	'wgProxyScriptPath',
	'wgSecretKey',
# Squid
	'wgInternalServer',
	'wgSquidServers',
	'wgSquidServersNoPurge',
# Img
	'wgFileStore',
	'wgHTTPProxy',
	'wgLocalFileRepo',
	'wgThumbnailScriptPath',
# Parser
	'wgTidyBin',
	'wgTidyConf',
# Special pages
	'wgRC2UDPAddress',
	'wgRC2UDPPort',
# Extensions
	'wgDisableInternalSearch',
	'wgExternalStores',
# htcp
	'wgHTCPMulticastAddress',
	'wgHTCPPort',
# Misc
	'wgMWSuggestTemplate',
	'wgOpenSearchTemplate',
);

/**
 * Settings that can be viewed only by users with 'viewconfig-all' right
 * because they can contain passwords
 */
$viewRestricted = array(
# Db
	'wgDBpassword',
	'wgDBservers',
	'wgLBFactoryConf',
	'wgExternalServers',
# Emal
	'wgSMTP',
# Proxy
	'wgProxyKey', # Deprecated
	'wgSecretKey',
);

/**
 * Array of settings that doesn't have to be modified, because they should only
 * be set by extensions, ...
 */
$notEditableSettings = array(
	'wgAPIListModules',
	'wgAPIMetaModules',
	'wgAPIModules',
	'wgAPIPropModules',
	'wgAjaxExportList',
	'wgAuth',
	'wgAutoloadClasses',
	'wgAvailableRights',
	'wgCommandLineMode',
	'wgConf',
	'wgDBconnection', // Too old
	'wgDBmysql4', // Too old
	'wgEditEncoding', // Too old
	'wgExceptionHooks',
	'wgExtensionAliasesFiles',
	'wgExtensionCredits',
	'wgExtensionFunctions',
	'wgExtensionMessagesFiles',
	'wgFilterCallback',
	'wgHooks',
	'wgInputEncoding', // Too old
	'wgJobClasses',
	'wgLogActions',
	'wgLogActionsHandlers',
	'wgLogHeaders',
	'wgLogNames',
	'wgLogTypes',
	'wgOutputEncoding', // Too old
	'wgPagePropLinkInvalidations',
	'wgParserOutputHooks',
	'wgParserTestFiles',
	'wgSkinExtensionFunctions',
	'wgSpecialPageCacheUpdates',
	'wgSpecialPages',
	'wgStyleSheetPath',
	'wgStyleVersion',
	'wgTrivialMimeDetection',
	'wgUseMemCached', // Too old
	'wgVersion',
);

/**
 * Array of settings depending of the Core version
 */
$settingsVersion = array();
