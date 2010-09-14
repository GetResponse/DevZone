grammar GetResponse::DynamicContent::Grammar {

    regex TOP {
        ^ <contents> $
    }

    regex contents {
        <plaintext>
        <chunk>*
    }

    regex chunk {
        <directive> <plaintext>
    }

    regex plaintext {
        [ <!before '{{' >. ]*
    }

    token directive {
        | <tag_predefined>
        | <tag_contact>
        | <tag_custom>
        | <tag_geo>
        | <tag_added_on>
        | <tag_link>
        | <tag_date>
        | <tag_timer>
        | <tag_random>
        | <tag_currency>
        | <tag_if>
    }

    token tag_start  { '{{' }
    token tag_end  { '}}' }
    token param_quote  { '"' }

    token param_any {
        <.param_quote> <-["]>+ <.param_quote>
    }

    token param_name {
        <.param_quote> \w+ <.param_quote>
    }

    token param_geo {
        <.param_quote>
            [
                'country_code' |
                'country' |
                'city' |
                'region' |
                'postal_code' |
                'dma_code' |
            ]
        <.param_quote>
    }

    token param_timestamp {
        <.param_quote>
            \d ** 4 '-' \d ** 2 '-' \d ** 2
            ' '
            \d ** 2 ':' \d ** 2 ':' \d ** 2
        <.param_quote>
    }

    token param_currency_amount {
        <.param_quote>
            \d+
        <.param_quote>
    }

    token param_currency_code {
        <.param_quote>
            <[A..Z]> ** 3
        <.param_quote>
    }

    regex tag_predefined {
        <.tag_start>
            <ws>
            'PREDEFINED'
            <ws>
            $<name>=<param_name>
            <ws>
        <.tag_end>
    }

    regex tag_contact {
        <.tag_start>
            <ws>
            'CONTACT'
            <ws>
            $<name>=<param_any> # TODO params for contact
            <ws>
        <.tag_end>
    }

    regex tag_custom {
        <.tag_start>
            <ws>
            'CUSTOM'
            <ws>
            $<name>=<param_name>
            <ws>
            [
                $<default>=<param_any>
                <ws>
            ]?
        <.tag_end>
    }

    regex tag_geo {
        <.tag_start>
            <ws>
            'GEO'
            <ws>
            $<name>=<param_geo>
            <ws>
        <.tag_end>
    }

    regex tag_added_on {
        <.tag_start>
            <ws>
            'ADDED_ON'
            <ws>
            $<format>=<param_any> # TODO params for added_on/date
            <ws>
        <.tag_end>
    }

    regex tag_link {
        <.tag_start>
            <ws>
            'LINK'
            <ws>
            $<url>=<param_any> # TODO params for link
            <ws>
            [
                $<name>=<param_any>
                <ws>
            ]?
        <.tag_end>
    }

    regex tag_date {
        <.tag_start>
            <ws>
            'DATE'
            <ws>
            $<format>=<param_any> # TODO params for added_on/date
            <ws>
            [
                $<modifier>=<param_any> # TODO params for date modifier
                <ws>
            ]?
        <.tag_end>
    }

    regex tag_timer {
        <.tag_start>
            <ws>
            'TIMER'
            <ws>
            $<timestamp>=<param_timestamp>
            <ws>
            $<format_before>=<param_any> # TODO params for timer format
            <ws>
            $<format_after>=<param_any> # TODO params for timer format
            <ws>
        <.tag_end>
    }

    regex tag_random {
        <.tag_start>
            <ws>
            'RANDOM'
            <ws>
            [
                $<content>=<param_any>
                <ws>
            ]+
        <.tag_end>
    }

    regex tag_currency {
        <.tag_start>
            <ws>
            'CURRENCY'
            <ws>
            $<amount>=<param_currency_amount>
            <ws>
            $<code>=<param_currency_code>
            <ws>
        <.tag_end>
    }

    regex tag_if {

        <.tag_start>
            <ws>
            'IF'
            <ws>
            $<condition>=<param_any>        # TODO params for conditions
            <ws>
        <.tag_end>
        <contents>

        [
            <.tag_start>
                <ws>
                'ELSIF'
                <ws>
                $<condition>=<param_any>        # TODO params for conditions
                <ws>
            <.tag_end>
            <contents>
        ]*

        [
            <.tag_start>
                <ws>
                'ELSE'
                <ws>
            <.tag_end>
            <contents>
        ]?

        <.tag_start>
            <ws>
            ENDIF
            <ws>
        <.tag_end>

    }

}


