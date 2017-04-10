<?php
defined('_JEXEC') or die;

class PlgContentTelegrambot extends JPlugin
{
    public function onContentBeforeSave($context, $article, $isNew)
    {
        $tagid = $this->params->get('tag', 0);
        $token = $this->params->get('token', 0);
        $channel = $this->params->get('channel', 0);
        $tags = new JHelperTags;
        $tags->getItemTags('com_content.article', $article->id, true);
        foreach ($tags->itemTags as $tag) {
            if ($tagid > 0 && $tag->id == $tagid) {
                $this->sendTelegram($token, $channel, $article);
            }
        }
    }
    public function sendTelegram($token, $channel, $article)
    {
        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
        $text = $article->title;
        $inlinekeys = [];
        $inlinekeys[] = array(
                          array(
                                  "text" => JText::_('PLG_CONTENT_TELEGRAMBOT_BUTTON_TEXT'),
                                  "url" => "http://".$_SERVER['HTTP_HOST'].JRoute::_("index.php?com_content&view=article&id=".$article->id)
                          )
                      );
        $inlinekeyboard = array("inline_keyboard" => $inlinekeys);
        $content = array(
                        "chat_id" => $channel,
                        "text" => $text,
                        "reply_markup" => $inlinekeyboard
                        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
