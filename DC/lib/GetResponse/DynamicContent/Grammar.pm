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
        | <tag_custom>
        | <tag_predefined>
        | <tag_geo>
        | <tag_random>
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

    regex tag_predefined {
        <.tag_start>
            <ws>
            'PREDEFINED'
            <ws>
            $<name>=<param_name>
            <ws>
        <.tag_end>
    }

    regex tag_geo {
        <.tag_start>
            <ws>
            'GEO'
            <ws>
            $<name>=<.param_geo>
            <ws>
        <.tag_end>
    }

    regex tag_random {
        <.tag_start>
            <ws>
            'RANDOM'
            <ws>
            [
                $<content>=<.param_any>
                <ws>
            ]+
        <.tag_end>
    }

    regex tag_if {

        <.tag_start>
            <ws>
            'IF'
            <ws>
            $<condition>=<.param_any>        # TODO params for conditions
            <ws>
        <.tag_end>
        <contents>

        [
            <.tag_start>
                <ws>
                'ELSIF'
                <ws>
                $<condition>=<.param_any>        # TODO params for conditions
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


