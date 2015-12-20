find ~/Library/Thunderbird/Profiles/ -name "INBOX" -exec grep "^Date: ...," {} \; | sed -e 's/Date: //g' > data.txt
