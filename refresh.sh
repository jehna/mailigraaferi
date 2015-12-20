find ~/Library/Thunderbird/Profiles/uhoze3oz.default/ImapMail/by2prd0611.outlook.com/INBOX.sbd -iregex ".*[^\.]..." -exec grep "^Date: ...," {} \; | sed -e 's/Date: //g' > data.txt
