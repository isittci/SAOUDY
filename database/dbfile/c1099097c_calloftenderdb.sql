--
-- PostgreSQL database dump
--

\restrict LVznHaWGJiKFJbd6xZmxZ6505MdpyezjlY2aLM2SJU1xeXVq0bYXtXbrnCdFl8N

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2025-12-31 15:11:48

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 246 (class 1259 OID 49014)
-- Name: alertes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.alertes (
    id uuid NOT NULL,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.alertes OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 48319)
-- Name: appels_offres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.appels_offres (
    id_appel_offre uuid NOT NULL,
    type_appel_offre_id uuid NOT NULL,
    numero_appel_offre character varying(20) NOT NULL,
    libelle_critere_appel_offre character varying(160) NOT NULL,
    objet_critere_appel_offre text NOT NULL,
    montant_global_appel_offre numeric(15,2) NOT NULL,
    description_critere_critere_appel_offre text NOT NULL,
    date_publication_critere_appel_offre timestamp(0) without time zone,
    date_limite_depot_critere_appel_offre timestamp(0) without time zone,
    date_ouverture_plis_critere_appel_offre timestamp(0) without time zone,
    statut_evaluation_critere_appel_offre character varying(255) DEFAULT '0'::character varying NOT NULL,
    conditions_participation_critere_appel_offre text,
    criteres_selection_critere_appel_offre text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT appels_offres_statut_evaluation_critere_appel_offre_check CHECK (((statut_evaluation_critere_appel_offre)::text = ANY ((ARRAY['1'::character varying, '0'::character varying])::text[])))
);


ALTER TABLE public.appels_offres OWNER TO postgres;

--
-- TOC entry 5434 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.type_appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.type_appel_offre_id IS 'Identifiant unique du type.';


--
-- TOC entry 5435 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.numero_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.numero_appel_offre IS 'Numéro officiel (ex: AOT-2025-045). Référence dans tous les documents.';


--
-- TOC entry 5436 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.libelle_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.libelle_critere_appel_offre IS 'Nom du lot (ex: Gros œuvre, Électricité, Plomberie).';


--
-- TOC entry 5437 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.objet_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.objet_critere_appel_offre IS 'Description officielle de ce qui est demandé.';


--
-- TOC entry 5438 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.montant_global_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.montant_global_appel_offre IS 'Montant total estimé pour cet appel d''offres.';


--
-- TOC entry 5439 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.description_critere_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.description_critere_critere_appel_offre IS 'Détail des travaux de ce critere';


--
-- TOC entry 5440 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.date_publication_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.date_publication_critere_appel_offre IS 'Date à laquelle l''appel d''offres a été publié.';


--
-- TOC entry 5441 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.date_limite_depot_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.date_limite_depot_critere_appel_offre IS 'Date limite pour le dépôt des offres.';


--
-- TOC entry 5442 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.date_ouverture_plis_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.date_ouverture_plis_critere_appel_offre IS 'Date prévue pour l''ouverture des plis.';


--
-- TOC entry 5443 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.statut_evaluation_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.statut_evaluation_critere_appel_offre IS 'Statut actuel de l''évaluation des offres. Pour savoir si actif ou non';


--
-- TOC entry 5444 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.conditions_participation_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.conditions_participation_critere_appel_offre IS 'Conditions requises pour participer à cet appel d''offres.';


--
-- TOC entry 5445 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.criteres_selection_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.criteres_selection_critere_appel_offre IS 'Critères utilisés pour évaluer les offres reçues.';


--
-- TOC entry 238 (class 1259 OID 48666)
-- Name: banques; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.banques (
    id_banque uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    nom_banque character varying(150),
    code_banque character varying(25) NOT NULL,
    numero_compte_banque character varying(25),
    code_guichet_banque character varying(25),
    cle_rib_banque character varying(25),
    iban_banque character varying(25),
    swift_bic_banque character varying(25),
    titulaire_compte_banque character varying(50),
    actif_banque boolean DEFAULT true,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.banques OWNER TO postgres;

--
-- TOC entry 5446 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.id_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.id_banque IS 'Identifiant unique de la banque.';


--
-- TOC entry 5447 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.prestataire_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.prestataire_id IS 'Identifiant du prestataire associé.';


--
-- TOC entry 5448 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.nom_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.nom_banque IS 'Nom de la banque';


--
-- TOC entry 5449 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.code_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.code_banque IS 'Code banque';


--
-- TOC entry 5450 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.numero_compte_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.numero_compte_banque IS 'Numéro de compte bancaire';


--
-- TOC entry 5451 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.code_guichet_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.code_guichet_banque IS 'Code guichet bancaire';


--
-- TOC entry 5452 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.cle_rib_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.cle_rib_banque IS 'Clé RIB (Relevé d''Identité Bancaire)';


--
-- TOC entry 5453 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.iban_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.iban_banque IS 'International Bank Account Number';


--
-- TOC entry 5454 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.swift_bic_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.swift_bic_banque IS 'SWIFT/BIC code';


--
-- TOC entry 5455 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.titulaire_compte_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.titulaire_compte_banque IS 'Nom du titulaire du compte bancaire';


--
-- TOC entry 5456 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.actif_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.actif_banque IS 'Permet de désactiver temporairement une banque sans la supprimer.';


--
-- TOC entry 5457 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.created_by IS 'Identifiant de l''utilisateur ayant créé la banque.';


--
-- TOC entry 5458 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.updated_by IS 'Identifiant de l''utilisateur ayant mis à jour la banque.';


--
-- TOC entry 5459 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.deleted_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.deleted_by IS 'Identifiant de l''utilisateur ayant supprimé la banque.';


--
-- TOC entry 5460 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.created_at IS 'Date de création de la banque.';


--
-- TOC entry 5461 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.updated_at IS 'Date de la dernière mise à jour de la banque.';


--
-- TOC entry 5462 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.deleted_at IS 'Date de suppression de la banque.';


--
-- TOC entry 239 (class 1259 OID 48702)
-- Name: capacites_techniques; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.capacites_techniques (
    id_capacite_technique uuid NOT NULL,
    prestataire_id uuid,
    effectif_permanent_capacite_technique integer,
    effectif_temporaire_capacite_technique integer,
    moyens_materiels_capacite_technique text,
    certifications_capacite_technique text,
    agrements_capacite_technique text,
    references_capacite_technique character varying(10),
    competences_cles_capacite_technique character varying(25),
    domaines_expertise_capacite_technique text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.capacites_techniques OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 48359)
-- Name: caracteristiques_appels_offres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.caracteristiques_appels_offres (
    id_caracteristique_appel_offre uuid CONSTRAINT caracteristiques_appels_off_id_caracteristique_appel_o_not_null NOT NULL,
    appel_offre_id uuid NOT NULL,
    version_caracteristique_appel_offre integer DEFAULT 1 CONSTRAINT caracteristiques_appels_off_version_caracteristique_ap_not_null NOT NULL,
    date_demarrage_prevue_caracteristique_appel_offre date,
    duree_estimee_jours_caracteristique_appel_offre integer,
    date_livraison_previsionnelle_caracteristique_appel_offre date,
    lieu_execution_caracteristique_appel_offre character varying(255),
    penalites_retard_journalier_caracteristique_appel_offre numeric(15,2),
    montant_garantie_caracteristique_appel_offre numeric(15,2),
    delai_garantie_jours_caracteristique_appel_offre numeric(15,2),
    conditions_paiement_caracteristique_appel_offre text,
    modalites_execution_caracteristique_appel_offre text,
    documents_requis_caracteristique_appel_offre text,
    is_active_caracteristique_appel_offre boolean DEFAULT true CONSTRAINT caracteristiques_appels_offres_is_active_not_null NOT NULL,
    autres_informations_caracteristique_appel_offre text,
    motif_modification_caracteristique_appel_offre text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    parent_id uuid
);


ALTER TABLE public.caracteristiques_appels_offres OWNER TO postgres;

--
-- TOC entry 5463 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.appel_offre_id IS 'Identifiant unique de l''appel d''offres.';


--
-- TOC entry 5464 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.version_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.version_caracteristique_appel_offre IS 'Version du critère pour le suivi des modifications.';


--
-- TOC entry 5465 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.date_demarrage_prevue_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.date_demarrage_prevue_caracteristique_appel_offre IS 'Date prévue de démarrage des travaux.';


--
-- TOC entry 5466 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.duree_estimee_jours_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.duree_estimee_jours_caracteristique_appel_offre IS 'Durée estimée des travaux en jours.';


--
-- TOC entry 5467 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.date_livraison_previsionnelle_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.date_livraison_previsionnelle_caracteristique_appel_offre IS 'Date prévue de livraison des travaux.';


--
-- TOC entry 5468 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.lieu_execution_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.lieu_execution_caracteristique_appel_offre IS 'Lieu prévu pour l''exécution des travaux.';


--
-- TOC entry 5469 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.penalites_retard_journalier_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.penalites_retard_journalier_caracteristique_appel_offre IS 'Montant de pénalité par jour de retard (ex: 50 000 FCFA/jour). Dissuasif.';


--
-- TOC entry 5470 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.montant_garantie_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.montant_garantie_caracteristique_appel_offre IS 'Caution de bonne exécution (souvent 5-10% du marché).';


--
-- TOC entry 5471 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.delai_garantie_jours_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.delai_garantie_jours_caracteristique_appel_offre IS 'Durée de garantie après réception (ex: 365 jours).';


--
-- TOC entry 5472 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.conditions_paiement_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.conditions_paiement_caracteristique_appel_offre IS 'Modalités (ex: 30% avance, 40% mi-parcours, 30% livraison).';


--
-- TOC entry 5473 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.modalites_execution_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.modalites_execution_caracteristique_appel_offre IS 'Exigences particulières.';


--
-- TOC entry 5474 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.documents_requis_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.documents_requis_caracteristique_appel_offre IS 'Liste des pièces à fournir (ex: [Attestation fiscale, Assurance, Caution]).';


--
-- TOC entry 5475 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.is_active_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.is_active_caracteristique_appel_offre IS 'Indique si cette version est active ou obsolète.';


--
-- TOC entry 5476 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.autres_informations_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.autres_informations_caracteristique_appel_offre IS 'Infos diverses.';


--
-- TOC entry 5477 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.motif_modification_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.motif_modification_caracteristique_appel_offre IS 'Pourquoi cette modification (ex: Demande du maître d''ouvrage, Erreur initiale, Force majeure).';


--
-- TOC entry 5478 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.parent_id IS 'Identifiant du critère parent, si applicable.';


--
-- TOC entry 234 (class 1259 OID 48495)
-- Name: criteres_evaluations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.criteres_evaluations (
    id_critere_evaluation uuid NOT NULL,
    lot_id uuid NOT NULL,
    numero_critere_evaluation character varying(20) NOT NULL,
    libelle_critere_evaluation character varying(160) NOT NULL,
    description_critere_evaluation text,
    note_reference_critere_evaluation numeric(8,2) DEFAULT '100'::numeric NOT NULL,
    statut_critere_evaluation character varying(255) DEFAULT '1'::character varying NOT NULL,
    ordre_execution_critere_evaluation integer,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT criteres_evaluations_statut_critere_evaluation_check CHECK (((statut_critere_evaluation)::text = ANY ((ARRAY['0'::character varying, '1'::character varying])::text[])))
);


ALTER TABLE public.criteres_evaluations OWNER TO postgres;

--
-- TOC entry 5479 (class 0 OID 0)
-- Dependencies: 234
-- Name: COLUMN criteres_evaluations.lot_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.criteres_evaluations.lot_id IS 'Identifiant du lot associé.';


--
-- TOC entry 5480 (class 0 OID 0)
-- Dependencies: 234
-- Name: COLUMN criteres_evaluations.note_reference_critere_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.criteres_evaluations.note_reference_critere_evaluation IS 'La note maximale qu''on peut obtenir';


--
-- TOC entry 237 (class 1259 OID 48635)
-- Name: documents; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.documents (
    id_document uuid NOT NULL,
    lot_id uuid,
    type_document character varying(20),
    titre_document character varying(100),
    fichier_nom_document character varying(100),
    fichier_path_document text,
    fichier_type_document character varying(50),
    fichier_taille_document numeric(10,2),
    description_document character varying(120),
    date_document timestamp(0) without time zone,
    version_document smallint,
    est_valide_document boolean,
    valide_par uuid,
    valide_at timestamp(0) without time zone,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.documents OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 48572)
-- Name: evaluations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.evaluations (
    id_evaluation uuid NOT NULL,
    version integer DEFAULT 1 NOT NULL,
    is_current boolean DEFAULT true NOT NULL,
    numero_evaluation character varying(50) NOT NULL,
    date_evaluation timestamp(0) without time zone,
    resultat_evaluation numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    note_maximale numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    pourcentage_final numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    rang integer,
    respo_technique_evaluation json,
    superviseur_evaluation json,
    evalue_par json,
    statut_evaluation smallint DEFAULT '0'::smallint NOT NULL,
    commentaire_general text,
    recommandation text,
    documents_evalues json,
    evaluateur_principal_id uuid,
    date_validation timestamp(0) without time zone,
    motif_validation text,
    valide_par uuid,
    date_rejet timestamp(0) without time zone,
    motif_rejet text,
    rejete_par uuid,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    evaluation_parent_id uuid,
    attribution_id uuid NOT NULL,
    critere_evaluation_id uuid
);


ALTER TABLE public.evaluations OWNER TO postgres;

--
-- TOC entry 5481 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.version; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.version IS 'Numéro de version de l''évaluation';


--
-- TOC entry 5482 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.is_current; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.is_current IS 'Indique si c''est la version active/courante';


--
-- TOC entry 5483 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.numero_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.numero_evaluation IS 'Numéro de l''évaluation (identique entre versions) - Généré automatiquement';


--
-- TOC entry 5484 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.date_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.date_evaluation IS 'Date de réalisation de l''évaluation';


--
-- TOC entry 5485 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.resultat_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.resultat_evaluation IS 'Note totale obtenue (somme des notes par critère)';


--
-- TOC entry 5486 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.note_maximale; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.note_maximale IS 'Note maximale possible (somme des notes de référence des critères)';


--
-- TOC entry 5487 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.pourcentage_final; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.pourcentage_final IS 'Pourcentage final (resultat_evaluation / note_maximale * 100)';


--
-- TOC entry 5488 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.rang; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.rang IS 'Rang parmi tous les prestataires évalués pour ce lot';


--
-- TOC entry 5489 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.respo_technique_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.respo_technique_evaluation IS 'Responsable technique: {nom_complet, email, telephone}';


--
-- TOC entry 5490 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.superviseur_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.superviseur_evaluation IS 'Superviseur: {nom_complet, email, telephone}';


--
-- TOC entry 5491 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.evalue_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.evalue_par IS 'Évaluateur: {nom_complet, email, telephone}';


--
-- TOC entry 5492 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.statut_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.statut_evaluation IS '0=En attente, 1=En cours, 2=Terminée, 3=Validée, 4=Rejetée';


--
-- TOC entry 5493 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.commentaire_general; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.commentaire_general IS 'Commentaire général sur l''évaluation';


--
-- TOC entry 5494 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.recommandation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.recommandation IS 'Recommandation pour l''attribution';


--
-- TOC entry 5495 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.documents_evalues; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.documents_evalues IS 'Liste des documents consultés pour l''évaluation';


--
-- TOC entry 5496 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.evaluateur_principal_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.evaluateur_principal_id IS 'Identifiant de l''évaluateur principal';


--
-- TOC entry 5497 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.date_validation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.date_validation IS 'Date de validation de l''évaluation';


--
-- TOC entry 5498 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.motif_validation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.motif_validation IS 'Motif en cas de validation de l''évaluation';


--
-- TOC entry 5499 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.valide_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.valide_par IS 'Identifiant de l''utilisateur ayant validé';


--
-- TOC entry 5500 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.date_rejet; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.date_rejet IS 'Date du rejet de l''évaluation';


--
-- TOC entry 5501 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.motif_rejet; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.motif_rejet IS 'Motif en cas de rejet de l''évaluation';


--
-- TOC entry 5502 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.rejete_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.rejete_par IS 'Identifiant de l''utilisateur ayant rejeté';


--
-- TOC entry 5503 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.created_by IS 'Identifiant de l''utilisateur créateur';


--
-- TOC entry 5504 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.updated_by IS 'Identifiant de l''utilisateur modificateur';


--
-- TOC entry 5505 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.deleted_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.deleted_by IS 'Identifiant de l''utilisateur suppresseur';


--
-- TOC entry 5506 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.created_at IS 'Date de création';


--
-- TOC entry 5507 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.updated_at IS 'Date de mise à jour';


--
-- TOC entry 5508 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.deleted_at IS 'Date de suppression logique';


--
-- TOC entry 5509 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.evaluation_parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.evaluation_parent_id IS 'Évaluation parente (chaînage des versions)';


--
-- TOC entry 5510 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN evaluations.attribution_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.attribution_id IS 'Référence vers l''attribution (prestataires_lots)';


--
-- TOC entry 245 (class 1259 OID 48956)
-- Name: evaluations_lots_prestataires; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.evaluations_lots_prestataires (
    id_evaluation_critere uuid NOT NULL,
    critere_evaluation_id uuid NOT NULL,
    evaluation_id uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    note_obtenue numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    note_reference numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    note_finale numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    pourcentage numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    conforme boolean DEFAULT false NOT NULL,
    observation text,
    justification text,
    documents_fournis json,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.evaluations_lots_prestataires OWNER TO postgres;

--
-- TOC entry 5511 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.critere_evaluation_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.critere_evaluation_id IS 'Critère d''évaluation';


--
-- TOC entry 5512 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.evaluation_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.evaluation_id IS 'Évaluation parente';


--
-- TOC entry 5513 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.prestataire_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.prestataire_id IS 'Prestataire évalué';


--
-- TOC entry 5514 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.note_obtenue; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.note_obtenue IS 'Note attribuée par l''évaluateur';


--
-- TOC entry 5515 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.note_reference; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.note_reference IS 'Note de référence du critère (copie pour historique)';


--
-- TOC entry 5516 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.note_finale; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.note_finale IS 'Note finale calculée (note_obtenue)';


--
-- TOC entry 5517 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.pourcentage; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.pourcentage IS 'Pourcentage (note_obtenue / note_reference * 100)';


--
-- TOC entry 5518 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.conforme; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.conforme IS 'Le critère est-il conforme?';


--
-- TOC entry 5519 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.observation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.observation IS 'Observation sur ce critère';


--
-- TOC entry 5520 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.justification; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.justification IS 'Justification de la note attribuée';


--
-- TOC entry 5521 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN evaluations_lots_prestataires.documents_fournis; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations_lots_prestataires.documents_fournis IS 'Documents fournis pour ce critère';


--
-- TOC entry 241 (class 1259 OID 48765)
-- Name: evaluations_prestataires; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.evaluations_prestataires (
    id_evaluation_prestataire uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    note_qualification_evaluation_prestataire numeric(20,2),
    date_derniere_evaluation_evaluation_prestataire timestamp(0) without time zone,
    nombre_contrats_executes_evaluation_prestataire numeric(20,2),
    taux_respect_delais_evaluation_prestataire numeric(20,2),
    taux_qualite_evaluation_prestataire numeric(20,2),
    nombre_litiges_evaluation_prestataire numeric(20,2),
    liste_statut_evaluation_prestataire character varying(25),
    date_mise_en_liste_evaluation_prestataire timestamp(0) without time zone,
    date_fin_sanction_evaluation_prestataire timestamp(0) without time zone,
    motif_liste_noire_evaluation_prestataire text,
    commentaire_evaluation_prestataire text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.evaluations_prestataires OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 48797)
-- Name: factures; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.factures (
    id_facture uuid NOT NULL,
    proforma_id uuid NOT NULL,
    numero_facture character varying(30) NOT NULL,
    montant_facture numeric(15,2) NOT NULL,
    date_facture date NOT NULL,
    date_reception_facture date NOT NULL,
    statut_facture character varying(255) DEFAULT 'en_attente'::character varying NOT NULL,
    comment_facture text,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT factures_statut_facture_check CHECK (((statut_facture)::text = ANY ((ARRAY['en_attente'::character varying, 'validee'::character varying, 'rejetee'::character varying, 'payee'::character varying, 'partiellement_payee'::character varying, 'annulee'::character varying])::text[])))
);


ALTER TABLE public.factures OWNER TO postgres;

--
-- TOC entry 5522 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.id_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.id_facture IS 'Identifiant unique de la facture au format UUID. Clé primaire générée automatiquement pour garantir l''unicité à travers tous les systèmes.';


--
-- TOC entry 5523 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.numero_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.numero_facture IS 'Numéro unique de la facture attribué par le prestataire. Format attendu: FAC-YYYY-XXXXX ou selon la nomenclature du prestataire. Sert de référence officielle dans tous les échanges et documents comptables.';


--
-- TOC entry 5524 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.montant_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.montant_facture IS 'Montant total TTC de la facture en FCFA. Doit correspondre au montant de la proforma validée (montant_retenu + TVA - remise + pénalités). Précision de 2 décimales pour les calculs comptables. Maximum: 9 999 999 999 999,99 FCFA.';


--
-- TOC entry 5525 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.date_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.date_facture IS 'Date d''émission de la facture par le prestataire. Date figurant sur le document officiel de facturation. Sert de référence pour le calcul des délais de paiement légaux.';


--
-- TOC entry 5526 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.date_reception_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.date_reception_facture IS 'Date de réception effective de la facture par le service gestionnaire. Point de départ du délai de traitement administratif. Important pour le respect des délais de paiement réglementaires (généralement 30 jours en marchés publics).';


--
-- TOC entry 5527 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.statut_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.statut_facture IS 'État actuel de la facture dans le workflow de traitement. Valeurs possibles: en_attente (réception, vérification en cours), validee (conforme, prête pour paiement), rejetee (non conforme, retournée au prestataire), payee (règlement total effectué), partiellement_payee (acompte versé), annulee (facture invalidée).';


--
-- TOC entry 5528 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.comment_facture; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.comment_facture IS 'Observations, remarques ou notes internes concernant la facture. Peut contenir: motifs de rejet, instructions particulières, références de documents complémentaires, historique des échanges avec le prestataire.';


--
-- TOC entry 5529 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.created_at IS 'Date et heure de création de l''enregistrement dans la base de données. Générée automatiquement lors de l''insertion. Format: YYYY-MM-DD HH:MM:SS.';


--
-- TOC entry 5530 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.updated_at IS 'Date et heure de la dernière modification de l''enregistrement. Mise à jour automatiquement par Eloquent à chaque sauvegarde. Permet de suivre la fraîcheur des données.';


--
-- TOC entry 5531 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN factures.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.factures.deleted_at IS 'Date de suppression logique (soft delete). Si non NULL, la facture est considérée comme supprimée mais reste en base pour archivage et audit. Permet la restauration ultérieure si nécessaire.';


--
-- TOC entry 224 (class 1259 OID 48160)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id uuid NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 48449)
-- Name: lots; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lots (
    id_lot uuid NOT NULL,
    appel_offre_id uuid NOT NULL,
    numero character varying(35) NOT NULL,
    libelle character varying(160),
    description_critere text,
    specifications_techniques text,
    motif_retrait text,
    version_lot integer DEFAULT 1 NOT NULL,
    date_attribution date,
    date_debut_prevue timestamp(0) without time zone,
    date_fin_prevue timestamp(0) without time zone,
    date_retrait date,
    attribution_lot character varying(255) DEFAULT '0'::character varying NOT NULL,
    statut_lot character varying(255),
    taux_penalites numeric(5,2),
    statut_retrait smallint,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    parent_id uuid,
    CONSTRAINT lots_attribution_lot_check CHECK (((attribution_lot)::text = ANY ((ARRAY['0'::character varying, '1'::character varying])::text[]))),
    CONSTRAINT lots_statut_lot_check CHECK (((statut_lot)::text = ANY ((ARRAY['0'::character varying, '1'::character varying])::text[])))
);


ALTER TABLE public.lots OWNER TO postgres;

--
-- TOC entry 5532 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN lots.appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.lots.appel_offre_id IS 'Identifiant de l''appel d''offres associé.';


--
-- TOC entry 5533 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN lots.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.lots.parent_id IS 'Identifiant du lot principal associé.';


--
-- TOC entry 220 (class 1259 OID 48086)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 48085)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 5534 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 243 (class 1259 OID 48837)
-- Name: paiements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.paiements (
    id_paiement uuid NOT NULL,
    facture_id uuid NOT NULL,
    banque_id uuid NOT NULL,
    montant_net_paye_paiement numeric(20,2),
    statut_paiement smallint,
    date_validation_paiement timestamp(0) without time zone,
    motif_rejet_paiement text,
    observations_paiement text,
    valide_par uuid,
    paye_par uuid,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    date_effectif_paiement date
);


ALTER TABLE public.paiements OWNER TO postgres;

--
-- TOC entry 5535 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.id_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.id_paiement IS 'Identifiant unique du paiement au format UUID. Clé primaire générée automatiquement garantissant l''unicité absolue de chaque transaction de paiement dans le système.';


--
-- TOC entry 5536 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.montant_net_paye_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.montant_net_paye_paiement IS 'Montant effectivement versé au prestataire en FCFA. Représente la somme nette créditée sur le compte bancaire. Peut différer du montant facturé en cas de: retenues de garantie, pénalités déduites, acomptes, ou paiements partiels. Précision de 2 décimales. Maximum: 99 999 999 999 999 999,99 FCFA.';


--
-- TOC entry 5537 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.statut_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.statut_paiement IS 'Code numérique indiquant l''état du paiement dans le workflow. Valeurs suggérées: 0=En attente de validation, 1=Validé/Approuvé, 2=En cours de traitement bancaire, 3=Payé/Exécuté, 4=Rejeté, 5=Annulé. Permet le suivi du cycle de vie complet du paiement.';


--
-- TOC entry 5538 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.date_validation_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.date_validation_paiement IS 'Date et heure exactes de la validation/approbation du paiement par l''autorité compétente. Marque le moment où le paiement est autorisé pour exécution. NULL tant que le paiement n''est pas validé. Important pour les délais de traitement et l''audit.';


--
-- TOC entry 5539 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.motif_rejet_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.motif_rejet_paiement IS 'Explication détaillée en cas de rejet du paiement. Doit préciser: la raison du rejet (pièces manquantes, erreur de montant, RIB invalide, etc.), les actions correctives requises, et les références réglementaires si applicable. Obligatoire si statut_paiement = Rejeté.';


--
-- TOC entry 5540 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.observations_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.observations_paiement IS 'Notes et commentaires libres concernant le paiement. Peut inclure: références du virement bancaire, numéro de bordereau, instructions particulières, historique des relances, ou toute information utile au suivi. Champ flexible pour documentation interne.';


--
-- TOC entry 5541 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.valide_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.valide_par IS 'Identifiant de l''utilisateur (ordonnateur ou responsable financier) ayant validé/approuvé le paiement. Représente l''autorité ayant donné le feu vert pour l''exécution du règlement. Essentiel pour la chaîne de responsabilité et la conformité aux procédures de contrôle interne.';


--
-- TOC entry 5542 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.paye_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.paye_par IS 'Identifiant de l''utilisateur (comptable ou trésorier) ayant effectivement exécuté le paiement. Distingué du validateur car souvent deux personnes différentes (séparation des tâches). Trace qui a physiquement déclenché le virement ou émis le chèque.';


--
-- TOC entry 5543 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.created_at IS 'Date et heure de création de l''enregistrement du paiement. Horodatage automatique lors de l''insertion. Représente le moment où la demande de paiement a été enregistrée, pas nécessairement la date d''exécution effective.';


--
-- TOC entry 5544 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.updated_at IS 'Date et heure de la dernière modification de l''enregistrement. Mis à jour automatiquement par Eloquent. Permet de connaître la fraîcheur des données et de détecter les modifications récentes.';


--
-- TOC entry 5545 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.deleted_at IS 'Date de suppression logique (soft delete) du paiement. Si renseignée, le paiement est considéré comme annulé/archivé mais reste en base pour la comptabilité et l''audit. Les paiements financiers ne doivent JAMAIS être supprimés définitivement pour conformité légale.';


--
-- TOC entry 5546 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN paiements.date_effectif_paiement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.paiements.date_effectif_paiement IS 'Date et heure exactes où le paiement a été effectivement réalisé (virement bancaire, chèque émis, etc.). Indique le moment où les fonds ont quitté le compte de l''organisation. Important pour la réconciliation bancaire et le suivi des délais de paiement.';


--
-- TOC entry 223 (class 1259 OID 48151)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 48192)
-- Name: permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.permissions (
    id uuid NOT NULL,
    name character varying(100) NOT NULL,
    slug character varying(100) NOT NULL,
    description text,
    resource character varying(100),
    action character varying(255) NOT NULL,
    guard_name character varying(50) DEFAULT 'web'::character varying NOT NULL,
    category character varying(100),
    priority smallint DEFAULT '0'::smallint NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    is_system boolean DEFAULT false NOT NULL,
    conditions json,
    created_by uuid,
    updated_by uuid,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT permissions_action_check CHECK (((action)::text = ANY ((ARRAY['create'::character varying, 'read'::character varying, 'update'::character varying, 'delete'::character varying, 'export'::character varying, 'import'::character varying, 'validate'::character varying, 'reject'::character varying, 'restore'::character varying, 'manage'::character varying, 'download'::character varying, 'duplicate'::character varying])::text[])))
);


ALTER TABLE public.permissions OWNER TO postgres;

--
-- TOC entry 5547 (class 0 OID 0)
-- Dependencies: 226
-- Name: TABLE permissions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.permissions IS 'Table des permissions du système de contrôle daccès';


--
-- TOC entry 5548 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.name IS 'Nom affiché de la permission';


--
-- TOC entry 5549 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.slug; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.slug IS 'Identifiant unique pour la permission';


--
-- TOC entry 5550 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.description; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.description IS 'Description détaillée de la permission';


--
-- TOC entry 5551 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.resource; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.resource IS 'Entité/ressource concernée (ex: users, posts)';


--
-- TOC entry 5552 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.action; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.action IS 'Action autorisée sur la ressource';


--
-- TOC entry 5553 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.guard_name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.guard_name IS 'Guard utilisé (web, api, etc.)';


--
-- TOC entry 5554 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.category; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.category IS 'Catégorie de permission pour groupement';


--
-- TOC entry 5555 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.priority; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.priority IS 'Priorité de la permission (0-255)';


--
-- TOC entry 5556 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.is_active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.is_active IS 'Permission active/inactive';


--
-- TOC entry 5557 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.is_system; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.is_system IS 'Permission système (non modifiable)';


--
-- TOC entry 5558 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.conditions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.conditions IS 'Conditions supplémentaires en JSON';


--
-- TOC entry 5559 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.created_by IS 'Membres qui a créé la permission';


--
-- TOC entry 5560 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.updated_by IS 'Dernier membres ayant modifié';


--
-- TOC entry 5561 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.last_used_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.last_used_at IS 'Dernière utilisation de la permission';


--
-- TOC entry 225 (class 1259 OID 48177)
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id uuid NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 48534)
-- Name: prestataires; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prestataires (
    id_prestataire uuid NOT NULL,
    raison_sociale_prestataire character varying(255) NOT NULL,
    numero_identification_prestataire character varying(25) NOT NULL,
    email_prestataire character varying(255) NOT NULL,
    numero_cc_prestataire character varying(50) NOT NULL,
    numero_rccm_prestataire character varying(50) NOT NULL,
    telephone_principal_prestataire character varying(20) NOT NULL,
    telephone_secondaire_prestataire character varying(20),
    adresse_prestataire text NOT NULL,
    ville_prestataire character varying(50) NOT NULL,
    pays_prestataire character varying(50) NOT NULL,
    representant_legal_prestataire json NOT NULL,
    statut_prestataire boolean DEFAULT false NOT NULL,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.prestataires OWNER TO postgres;

--
-- TOC entry 5562 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN prestataires.numero_cc_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.numero_cc_prestataire IS 'Numéro de la carte de contribuable';


--
-- TOC entry 5563 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN prestataires.numero_rccm_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.numero_rccm_prestataire IS 'Numéro du Registre de Commerce et du Crédit Mobilier';


--
-- TOC entry 5564 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN prestataires.telephone_principal_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.telephone_principal_prestataire IS 'Numéro de téléphone principal du prestataire';


--
-- TOC entry 5565 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN prestataires.telephone_secondaire_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.telephone_secondaire_prestataire IS 'Numéro de téléphone secondaire du prestataire';


--
-- TOC entry 5566 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN prestataires.adresse_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.adresse_prestataire IS 'Adresse physique du prestataire';


--
-- TOC entry 5567 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN prestataires.representant_legal_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.representant_legal_prestataire IS 'Informations sur le représentant légal au format JSON (tableau de represents): id, statut, nom, prenoms, contact, email, nationalité, pays, adresse, profession, date_naissance, lieu_naissance, numero_piece_identite, type_piece_identite, date_delivrance, lieu_delivrance, date_expiration.';


--
-- TOC entry 244 (class 1259 OID 48875)
-- Name: prestataires_lots; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prestataires_lots (
    id_attribution uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    lot_id uuid NOT NULL,
    proforma_id uuid NOT NULL,
    version_attribution integer DEFAULT 1 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    numero_attribution character varying(30),
    date_attribution date,
    date_debut_prevue date,
    date_fin_prevue date,
    date_debut_reelle date,
    date_fin_reelle date,
    statut_attribution smallint DEFAULT '0'::smallint NOT NULL,
    motif_suspension text,
    date_suspension timestamp(0) without time zone,
    date_reprise_prevue date,
    date_reprise_reelle date,
    motif_retrait text,
    date_retrait timestamp(0) without time zone,
    type_retrait character varying(255),
    jours_retard integer DEFAULT 0 NOT NULL,
    taux_penalites numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    penalites_appliquees numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    penalites_payees numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    pourcentage_avancement numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    montant_engage numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    montant_paye numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    observations text,
    conditions_particulieres text,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    parent_attribution_id uuid,
    CONSTRAINT prestataires_lots_type_retrait_check CHECK (((type_retrait)::text = ANY ((ARRAY['volontaire'::character varying, 'force'::character varying, 'resiliation'::character varying, 'abandon'::character varying])::text[])))
);


ALTER TABLE public.prestataires_lots OWNER TO postgres;

--
-- TOC entry 5568 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.id_attribution; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.id_attribution IS 'Identifiant unique de l''attribution';


--
-- TOC entry 5569 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.prestataire_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.prestataire_id IS 'Prestataire attributaire';


--
-- TOC entry 5570 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.lot_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.lot_id IS 'Lot concerné';


--
-- TOC entry 5571 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.proforma_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.proforma_id IS 'Proforma associée';


--
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.version_attribution; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.version_attribution IS 'Version de l''attribution (incrémente à chaque réattribution)';


--
-- TOC entry 5573 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.is_active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.is_active IS 'TRUE = attribution active, FALSE = historique';


--
-- TOC entry 5574 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.numero_attribution; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.numero_attribution IS 'Numéro unique (ex: ATT-2025-001)';


--
-- TOC entry 5575 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_attribution; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_attribution IS 'Date officielle d''attribution';


--
-- TOC entry 5576 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_debut_prevue; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_debut_prevue IS 'Date de début prévue';


--
-- TOC entry 5577 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_fin_prevue; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_fin_prevue IS 'Date de fin prévue';


--
-- TOC entry 5578 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_debut_reelle; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_debut_reelle IS 'Date réelle de début';


--
-- TOC entry 5579 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_fin_reelle; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_fin_reelle IS 'Date réelle de fin';


--
-- TOC entry 5580 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.statut_attribution; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.statut_attribution IS '0=En attente, 1=Attribué, 2=Suspendu, 3=Retiré, 4=Terminé, 5=Annulé';


--
-- TOC entry 5581 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.motif_suspension; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.motif_suspension IS 'Raison de la suspension';


--
-- TOC entry 5582 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_suspension; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_suspension IS 'Date de suspension';


--
-- TOC entry 5583 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_reprise_prevue; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_reprise_prevue IS 'Date prévue de reprise';


--
-- TOC entry 5584 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_reprise_reelle; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_reprise_reelle IS 'Date réelle de reprise';


--
-- TOC entry 5585 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.motif_retrait; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.motif_retrait IS 'Raison du retrait';


--
-- TOC entry 5586 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.date_retrait; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_retrait IS 'Date du retrait';


--
-- TOC entry 5587 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.type_retrait; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.type_retrait IS 'Type de retrait';


--
-- TOC entry 5588 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.jours_retard; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.jours_retard IS 'Jours de retard accumulés';


--
-- TOC entry 5589 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.taux_penalites; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.taux_penalites IS 'Taux de pénalités (%)';


--
-- TOC entry 5590 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.penalites_appliquees; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.penalites_appliquees IS 'Montant des pénalités';


--
-- TOC entry 5591 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.penalites_payees; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.penalites_payees IS 'Pénalités payées';


--
-- TOC entry 5592 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.pourcentage_avancement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.pourcentage_avancement IS 'Avancement (0-100%)';


--
-- TOC entry 5593 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.montant_engage; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.montant_engage IS 'Montant engagé';


--
-- TOC entry 5594 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.montant_paye; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.montant_paye IS 'Montant payé';


--
-- TOC entry 5595 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.observations; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.observations IS 'Observations';


--
-- TOC entry 5596 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.conditions_particulieres; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.conditions_particulieres IS 'Conditions particulières';


--
-- TOC entry 5597 (class 0 OID 0)
-- Dependencies: 244
-- Name: COLUMN prestataires_lots.parent_attribution_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.parent_attribution_id IS 'Attribution précédente (chaînage)';


--
-- TOC entry 232 (class 1259 OID 48400)
-- Name: proformas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.proformas (
    id_proforma uuid NOT NULL,
    version_proforma integer DEFAULT 1 NOT NULL,
    numero_proforma character varying(20) NOT NULL,
    date_proforma date NOT NULL,
    date_debut_validee_proforma date CONSTRAINT proformas_date_debut_validee_not_null NOT NULL,
    date_redemarrage_proforma date CONSTRAINT proformas_date_redemarrage_not_null NOT NULL,
    date_fin_validee_proforma date CONSTRAINT proformas_date_fin_validee_not_null NOT NULL,
    montant_retenu_proforma numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    taxe_montant numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    remise_montant_proforma numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    modalite_proforma character varying(255),
    penalites_proforma numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    motif_modification_proforma text,
    actif_proforma boolean DEFAULT true NOT NULL,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    parent_id uuid
);


ALTER TABLE public.proformas OWNER TO postgres;

--
-- TOC entry 5598 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.version_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.version_proforma IS 'Version du critère pour le suivi des modifications.';


--
-- TOC entry 5599 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.numero_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.numero_proforma IS ' Référence dans tous les documents. Généré automatiquement dans le controller';


--
-- TOC entry 5600 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.date_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.date_proforma IS 'Date de création de la proforma.';


--
-- TOC entry 5601 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.date_debut_validee_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.date_debut_validee_proforma IS 'Date du début valitéé.';


--
-- TOC entry 5602 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.date_redemarrage_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.date_redemarrage_proforma IS 'Date de redemarrage validée.';


--
-- TOC entry 5603 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.date_fin_validee_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.date_fin_validee_proforma IS 'Date  de fin validée.';


--
-- TOC entry 5604 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.taxe_montant; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.taxe_montant IS 'TVA par defaut 18% du montent retenu';


--
-- TOC entry 5605 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.remise_montant_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.remise_montant_proforma IS 'La rémise (reduction)';


--
-- TOC entry 5606 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.modalite_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.modalite_proforma IS 'Modalités de paiement spécifiées dans la proforma.';


--
-- TOC entry 5607 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.penalites_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.penalites_proforma IS 'Pénalités associées à la proforma.';


--
-- TOC entry 5608 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.motif_modification_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.motif_modification_proforma IS 'Pourquoi cette modification (ex: Demande du maître d''ouvrage, Erreur initiale, Force majeure).';


--
-- TOC entry 5609 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.actif_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.actif_proforma IS 'Permet de désactiver temporairement une proforma sans la supprimer.';


--
-- TOC entry 5610 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.parent_id IS 'Identifiant du critère parent, si applicable.';


--
-- TOC entry 227 (class 1259 OID 48232)
-- Name: role_permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.role_permissions (
    role_id uuid NOT NULL,
    permission_id uuid NOT NULL,
    attribue_par uuid,
    attribue_le timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    expire_le timestamp(0) without time zone,
    actif boolean DEFAULT true NOT NULL,
    conditions json,
    notes text,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.role_permissions OWNER TO postgres;

--
-- TOC entry 5611 (class 0 OID 0)
-- Dependencies: 227
-- Name: TABLE role_permissions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.role_permissions IS 'Table pivot : association entre rôles et permissions';


--
-- TOC entry 5612 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.role_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.role_id IS 'ID du rôle';


--
-- TOC entry 5613 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.permission_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.permission_id IS 'ID de la permission';


--
-- TOC entry 5614 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.attribue_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.attribue_par IS 'ID de l''utilisateur qui a attribué cette permission au rôle';


--
-- TOC entry 5615 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.attribue_le; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.attribue_le IS 'Date et heure d''attribution de la permission';


--
-- TOC entry 5616 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.expire_le; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.expire_le IS 'Date d''expiration (pour permissions temporaires)';


--
-- TOC entry 5617 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.actif; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.actif IS 'Permission active pour ce rôle';


--
-- TOC entry 5618 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.conditions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.conditions IS 'Conditions spécifiques pour cette attribution';


--
-- TOC entry 5619 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.notes; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.notes IS 'Notes sur l''attribution de cette permission';


--
-- TOC entry 221 (class 1259 OID 48095)
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id uuid NOT NULL,
    name character varying(100) NOT NULL,
    slug character varying(100) NOT NULL,
    description text,
    level integer NOT NULL,
    is_system_role boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 48282)
-- Name: role_permissions_actifs; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.role_permissions_actifs AS
 SELECT rp.role_id,
    rp.permission_id,
    rp.attribue_le,
    rp.expire_le,
    r.name AS nom_role,
    r.slug AS code_role,
    r.level AS niveau_role,
    p.name AS nom_permission,
    p.slug AS code_permission,
    p.resource,
    p.action,
    p.category AS categorie_permission
   FROM ((public.role_permissions rp
     JOIN public.roles r ON ((rp.role_id = r.id)))
     JOIN public.permissions p ON ((rp.permission_id = p.id)))
  WHERE ((rp.actif = true) AND (rp.deleted_at IS NULL) AND (r.deleted_at IS NULL) AND (p.is_active = true) AND (p.deleted_at IS NULL) AND ((rp.expire_le IS NULL) OR (rp.expire_le > now())));


ALTER VIEW public.role_permissions_actifs OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 48733)
-- Name: situations_financieres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.situations_financieres (
    id_situation_financiere uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    exercice_fiscal_situation_financiere character varying(36),
    chiffre_affaire_situation_financiere numeric(20,2),
    fonds_propres_situation_financiere numeric(20,2),
    capacite_emprunt_situation_financiere numeric(20,2),
    ratio_solvabilite_situation_financiere numeric(20,2),
    ratio_liquidite_situation_financiere numeric(20,2),
    resultat_net_situation_financiere numeric(20,2),
    total_actif_situation_financiere numeric(20,2),
    total_passif_situation_financiere numeric(20,2),
    observations_situation_financiere text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.situations_financieres OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 48287)
-- Name: types_appels_offres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.types_appels_offres (
    id_type_appel_offre uuid NOT NULL,
    libelle_type_appel_offre character varying(160) NOT NULL,
    code_type_appel_offre character varying(10) NOT NULL,
    valeur_minimuim_type_appel_offre numeric(15,2) NOT NULL,
    valeur_maximuim_type_appel_offre numeric(15,2) NOT NULL,
    description_critere_type_appel_offre text,
    actif_type_appel_offre boolean DEFAULT true NOT NULL,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    parent_id uuid,
    version_type_appel_offre integer DEFAULT 1 NOT NULL,
    motif_modification_type_appel_offre character varying(255)
);


ALTER TABLE public.types_appels_offres OWNER TO postgres;

--
-- TOC entry 5620 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.libelle_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.libelle_type_appel_offre IS 'Libellé du type d''appel d''offres';


--
-- TOC entry 5621 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.code_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.code_type_appel_offre IS 'Code court (ex: AOT, AOS, AOF). Utilisé dans les numéros d''AO.';


--
-- TOC entry 5622 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.valeur_minimuim_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.valeur_minimuim_type_appel_offre IS 'Valeur minimale associée au type d''appel d''offres';


--
-- TOC entry 5623 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.valeur_maximuim_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.valeur_maximuim_type_appel_offre IS 'Valeur maximale associée au type d''appel d''offres';


--
-- TOC entry 5624 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.description_critere_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.description_critere_type_appel_offre IS 'Description détaillée du type d''appel d''offres';


--
-- TOC entry 5625 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.actif_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.actif_type_appel_offre IS 'Permet de désactiver temporairement un type sans le supprimer.';


--
-- TOC entry 5626 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.parent_id IS 'Identifiant du critère parent, si applicable.';


--
-- TOC entry 222 (class 1259 OID 48112)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id uuid NOT NULL,
    nom_complet character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    telephone_principal character varying(255),
    telephone_secondaire character varying(255),
    role_id uuid NOT NULL,
    email_verified_at timestamp(0) without time zone,
    statut character varying(255) DEFAULT '0'::character varying NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    CONSTRAINT users_statut_check CHECK (((statut)::text = ANY ((ARRAY['1'::character varying, '0'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 4960 (class 2604 OID 48089)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 5428 (class 0 OID 49014)
-- Dependencies: 246
-- Data for Name: alertes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.alertes (id, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5412 (class 0 OID 48319)
-- Dependencies: 230
-- Data for Name: appels_offres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.appels_offres (id_appel_offre, type_appel_offre_id, numero_appel_offre, libelle_critere_appel_offre, objet_critere_appel_offre, montant_global_appel_offre, description_critere_critere_appel_offre, date_publication_critere_appel_offre, date_limite_depot_critere_appel_offre, date_ouverture_plis_critere_appel_offre, statut_evaluation_critere_appel_offre, conditions_participation_critere_appel_offre, criteres_selection_critere_appel_offre, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0ad6fd8-b11a-4eef-98d6-6a74a8e9d54d	a0ad6950-63ec-4588-a41c-14244604c6ec	CDPPT-2025-TIAS02	CONSTRUCTION DE DEUX ECOLE PRIMAIRE PUBLIQUE A TIASSALE	Le présent appel d’offres a pour objet la sélection d’une entreprise qualifiée pour la construction de deux (02) écoles primaires publiques à Tiassalé, dans le but de renforcer les infrastructures éducatives, d’améliorer l’accès à l’enseignement primaire et de contribuer à l’amélioration des conditions d’apprentissage des élèves.\r\n\r\nLes travaux à réaliser comprennent l’ensemble des prestations nécessaires à la réalisation complète des infrastructures, depuis les études techniques jusqu’à la livraison des ouvrages prêts à l’usage, conformément aux normes en vigueur.	195000000.00	Le projet porte sur la construction complète de deux écoles primaires publiques, incluant notamment :\r\na) Travaux de construction\r\nTravaux préparatoires et d’installation de chantier\r\nTerrassement et fondations\r\nConstruction des bâtiments scolaires (salles de classe, bureaux administratifs, magasins, sanitaires)\r\nRéalisation des murs, toitures, plafonds et menuiseries (bois, aluminium ou métallique)\r\nTravaux de revêtement (carrelage, peinture intérieure et extérieure)\r\n\r\nb) Équipements et aménagements\r\nInstallation électrique complète (éclairage, prises, tableaux électriques)\r\nInstallation sanitaire (points d’eau, latrines, fosses septiques ou systèmes adaptés)\r\nAménagement des cours d’école et des voies d’accès\r\nClôture et portails de sécurité (si requis)\r\n\r\nc) Normes et exigences\r\nRespect des normes de construction en vigueur en Côte d’Ivoire\r\nPrise en compte des règles de sécurité, d’hygiène et d’accessibilité\r\nUtilisation de matériaux durables et de qualité\r\nRespect des délais contractuels d’exécution\r\n\r\nd) Livraison\r\nRéception provisoire et définitive des ouvrages\r\nRemise des plans de récolement et documents techniques\r\nMise à disposition d’infrastructures fonctionnelles, sécurisées et adaptées à l’enseignement primaire	2025-12-25 00:00:00	2025-12-26 00:00:00	2025-12-27 00:00:00	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 13:01:02	2025-12-25 13:01:02	\N
a0af4a2a-5f0b-4464-9cf6-d564b102c40f	a0af47a5-0c9b-4715-8616-40e2e7e15fe1	CDB	CONSTRUCTION DE BIBLIOTHEQUE	Le présent appel d’offres a pour objet la construction d’une bibliothèque destinée à favoriser l’accès à la connaissance, à la lecture et à la recherche, au profit des élèves, étudiants et de la communauté locale.\r\nLe projet vise la réalisation d’une infrastructure moderne, fonctionnelle et durable, conforme aux normes techniques, architecturales et environnementales en vigueur.	215000000.00	Les travaux à réaliser dans le cadre du présent marché comprennent la construction complète de la bibliothèque, incluant les études, les travaux de gros œuvre, de second œuvre, ainsi que les équipements essentiels à son fonctionnement.\r\n\r\nDe manière non exhaustive, les prestations comprennent :\r\n\r\nLes études techniques et architecturales d’exécution\r\n\r\nLes travaux de terrassement et de fondation\r\n\r\nLa réalisation du gros œuvre (élévation, dallage, charpente, couverture)\r\n\r\nLes travaux de second œuvre (maçonnerie, menuiserie, plomberie, électricité, peinture, revêtements)\r\n\r\nL’aménagement des espaces intérieurs (salles de lecture, rayonnages, bureaux, espaces numériques)\r\n\r\nL’installation des équipements électriques, informatiques et de sécurité\r\n\r\nLa mise en conformité aux normes de sécurité, d’accessibilité et de protection incendie\r\n\r\nLes essais, contrôles et la réception des ouvrages\r\n\r\nL’ouvrage devra être livré clé en main, prêt à l’exploitation, dans le respect des délais contractuels et des exigences de qualité définies par le maître d’ouvrage.	2025-12-26 00:00:00	2025-12-27 00:00:00	2025-12-28 00:00:00	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 11:07:20	2025-12-29 12:41:21	\N
a0b5cc10-5576-4f4d-b604-f15713576d6e	a0af47a5-0c9b-4715-8616-40e2e7e15fe1	AOC-2025-002	DEVELOPPEMENT DES PROJET AGRICOLES	Le présent appel d’offres a pour objet la sélection d’un ou plusieurs prestataires qualifiés pour la mise en œuvre de projets agricoles intégrés, visant à améliorer la productivité, la durabilité et la rentabilité des exploitations agricoles, tout en contribuant à la sécurité alimentaire, à la création d’emplois et au développement socio-économique des zones d’intervention.	215000000.00	Description Détaillée des Prestations\r\n\r\nLes prestations attendues dans le cadre du présent appel d’offres comprennent, sans s’y limiter, les activités suivantes :\r\n\r\n2.1. Études et planification\r\nRéalisation de diagnostics agricoles et agro-économiques des zones ciblées ;\r\nIdentification des filières agricoles prioritaires et à fort potentiel ;\r\nÉlaboration de plans de développement agricole adaptés aux réalités locales.\r\n\r\n2.2. Mise en œuvre des projets agricoles\r\nAménagement et mise en valeur des périmètres agricoles (préparation des sols, irrigation, drainage, etc.) ;\r\nFourniture et installation d’équipements agricoles et d’intrants (semences améliorées, engrais, matériel agricole) ;\r\nMise en place de pratiques agricoles modernes, durables et respectueuses de l’environnement.\r\n\r\n2.3. Renforcement des capacités\r\nFormation et encadrement des producteurs et acteurs locaux sur les techniques agricoles améliorées ;\r\nAppui à l’organisation des producteurs (coopératives, groupements, associations) ;\r\nSensibilisation aux bonnes pratiques environnementales et à la gestion durable des ressources naturelles.\r\n\r\n2.4. Suivi, évaluation et accompagnement\r\nMise en place de mécanismes de suivi-évaluation des activités et des performances des projets ;\r\nAssistance technique continue durant la phase de mise en œuvre ;\r\nÉlaboration de rapports périodiques et finaux sur l’état d’avancement et les résultats obtenus.\r\n\r\n2.5. Résultats attendus\r\nAmélioration significative des rendements agricoles ;\r\nAugmentation des revenus des producteurs ;\r\nRenforcement de la sécurité alimentaire locale ;\r\nContribution au développement économique et social des zones concernées.	2025-12-29 00:00:00	2025-12-30 00:00:00	2025-12-31 00:00:00	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-29 16:45:31	2025-12-29 17:21:07	\N
\.


--
-- TOC entry 5420 (class 0 OID 48666)
-- Dependencies: 238
-- Data for Name: banques; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.banques (id_banque, prestataire_id, nom_banque, code_banque, numero_compte_banque, code_guichet_banque, cle_rib_banque, iban_banque, swift_bic_banque, titulaire_compte_banque, actif_banque, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0ae0b56-e1a8-4753-987f-d12f5d29544d	a0ae078c-1897-48de-8e7a-867ef2d066d8	Bank of Africa	CI008	0123456789	01001	85	CI93CI0080104130854900185	SGBFCIAB	SOCIETE GENERALE DE CONSTRUCTION NAVALE	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 20:15:49	2025-12-25 20:16:30	\N
a0af6d69-c12d-49bc-9e1b-a0b4c7b8eade	a0af6c59-ef06-4bf7-97ef-fec58547831c	ECOBANK	CO520	0120214520	021201	65	\N	\N	KOUASSI KAN	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 12:45:53	2025-12-26 12:45:53	\N
\.


--
-- TOC entry 5421 (class 0 OID 48702)
-- Dependencies: 239
-- Data for Name: capacites_techniques; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.capacites_techniques (id_capacite_technique, prestataire_id, effectif_permanent_capacite_technique, effectif_temporaire_capacite_technique, moyens_materiels_capacite_technique, certifications_capacite_technique, agrements_capacite_technique, references_capacite_technique, competences_cles_capacite_technique, domaines_expertise_capacite_technique, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0b9a96d-fa96-4e08-98b0-619572bdc9c0	a0af6c59-ef06-4bf7-97ef-fec58547831c	55	25	Matériels et équipements techniques spécialisés\r\nVéhicules utilitaires et logistiques\r\nOutillages professionnels\r\nÉquipements informatiques et logiciels spécialisés	ISO 9001 (Management de la qualité),ISO 14001 (Management environnemental),ISO 45001 (Santé et sécurité au travail)	Agrément ministériel ou sectoriel,Autorisation d’exercer,Inscription au registre professionnel,Agrément des organismes de régulation	75+	BTP ET INFORMATIQUE	BTP, agriculture, informatique, hydraulique, énergie	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 14:51:59	2025-12-31 15:02:32	\N
\.


--
-- TOC entry 5413 (class 0 OID 48359)
-- Dependencies: 231
-- Data for Name: caracteristiques_appels_offres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.caracteristiques_appels_offres (id_caracteristique_appel_offre, appel_offre_id, version_caracteristique_appel_offre, date_demarrage_prevue_caracteristique_appel_offre, duree_estimee_jours_caracteristique_appel_offre, date_livraison_previsionnelle_caracteristique_appel_offre, lieu_execution_caracteristique_appel_offre, penalites_retard_journalier_caracteristique_appel_offre, montant_garantie_caracteristique_appel_offre, delai_garantie_jours_caracteristique_appel_offre, conditions_paiement_caracteristique_appel_offre, modalites_execution_caracteristique_appel_offre, documents_requis_caracteristique_appel_offre, is_active_caracteristique_appel_offre, autres_informations_caracteristique_appel_offre, motif_modification_caracteristique_appel_offre, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id) FROM stdin;
a0ad74f3-7ceb-4d91-ab4d-c9db017eaedb	a0ad6fd8-b11a-4eef-98d6-6a74a8e9d54d	1	2025-12-25	6	2025-12-31	TIASSALE (Région d'Agnéby-Tiassa)	\N	\N	\N	\N	\N	\N	t	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 13:15:19	2025-12-25 13:15:19	\N	\N
a0af4c29-80d7-4ef4-8e2c-e5f344fc7ff2	a0af4a2a-5f0b-4464-9cf6-d564b102c40f	1	2025-12-28	34	2026-01-31	DISTRICT DE YAMOUSSOUKRO	\N	\N	\N	\N	\N	\N	t	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 11:12:55	2025-12-26 11:12:55	\N	\N
a0b5dded-5aa9-4c36-b923-784563fd3cc8	a0b5cc10-5576-4f4d-b604-f15713576d6e	1	2025-12-31	11	2026-01-11	District Autonome de Yamoussoukro	\N	\N	\N	\N	\N	\N	t	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-29 17:35:28	2025-12-29 17:35:28	\N	\N
\.


--
-- TOC entry 5416 (class 0 OID 48495)
-- Dependencies: 234
-- Data for Name: criteres_evaluations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.criteres_evaluations (id_critere_evaluation, lot_id, numero_critere_evaluation, libelle_critere_evaluation, description_critere_evaluation, note_reference_critere_evaluation, statut_critere_evaluation, ordre_execution_critere_evaluation, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0b78a31-cfa8-469f-b9fd-e49be228ade7	a0b75ab3-080b-41d0-9eab-ea2f12b25190	CRIT-001	Qualité et pertinence de l’analyse diagnostique	Appréciation de la capacité du soumissionnaire à analyser correctement le contexte du projet (technique, environnemental, socio-économique).	50.00	1	1	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:32:59	2025-12-30 13:32:59	\N
a0b78a6a-8a7d-429c-810f-e212dc321822	a0b75ab3-080b-41d0-9eab-ea2f12b25190	CRIT-002	Méthodologie et outils d’étude proposés	Évaluation de la pertinence des méthodes, outils et approches techniques utilisés pour les études préliminaires.	50.00	1	2	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:33:36	2025-12-30 13:33:36	\N
a0b7934a-9b1a-49cb-83a2-f26d55411dd3	a0b75b2a-ae5b-4e68-90a5-9a8902d80cac	CRIT-001	Pertinence et cohérence de la solution technique proposée	Évaluation de l’adéquation des solutions techniques proposées avec les résultats des études préliminaires et les objectifs du projet.	80.00	1	1	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:58:26	2025-12-30 13:58:26	\N
a0b79385-af9f-43aa-99a1-29ad9f01a27f	a0b75b2a-ae5b-4e68-90a5-9a8902d80cac	CRIT-002	Qualité des études techniques et des livrables	Appréciation de la qualité, de la précision et de l’exploitabilité des documents techniques fournis.\r\n\r\nÉléments d’évaluation :\r\n\r\nClarté et exhaustivité des plans, schémas et notes de calcul\r\n\r\nNiveau de détail de l’APS et de l’APD\r\n\r\nFaisabilité technique et optimisation des coûts de réalisation	20.00	1	2	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:59:04	2025-12-30 13:59:04	\N
a0ae147c-080e-46b5-be1a-82cb41dd6374	a0ae0f46-357b-43e2-af5e-f2e422a91e41	CRIT-006	Délai et planning de livraison	Mesure la capacité du soumissionnaire à respecter les délais de livraison proposés, la pertinence du planning, ainsi que la rapidité de mobilisation des moyens logistiques.	15.00	1	4	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 20:41:24	2025-12-25 20:58:30	\N
a0ae131a-6d1b-4a5e-a5ed-d845fc514104	a0ae0f46-357b-43e2-af5e-f2e422a91e41	CRIT-005	Garanties, service après-livraison et engagements	Apprécie les garanties offertes, les modalités de remplacement des matériaux non conformes, le service après-livraison et les engagements contractuels du soumissionnaire.	5.00	1	5	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 20:37:32	2025-12-25 20:58:41	\N
a0ae1277-3d61-40b2-b013-8314f03f55c3	a0ae0f46-357b-43e2-af5e-f2e422a91e41	CRIT-003	Capacité logistique et moyens matériels	Apprécie les moyens de transport, d’entreposage, de manutention et l’organisation logistique mise en place pour assurer une livraison efficace et sécurisée des matériaux.	10.00	1	6	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 20:35:45	2025-12-25 20:58:41	\N
a0ae1192-b819-4da5-b307-d0979c3ac137	a0ae0f46-357b-43e2-af5e-f2e422a91e41	CRIT-001	Conformité technique des matériels proposés	Apprécie le degré de conformité des matériaux proposés aux spécifications techniques du dossier d’appel d’offres, aux normes en vigueur et à la qualité requise (certificats, fiches techniques, caractéristiques physiques et mécaniques).	30.00	1	2	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 20:33:15	2025-12-25 21:03:56	\N
a0af51a0-0987-4026-9881-e5bbdb740974	a0af507f-239c-4b4b-8598-34f54238aa16	CRIT-001	Conformité technique des matériels proposés	Apprécie la conformité des matériaux aux spécifications techniques, aux normes en vigueur et à la qualité exigée (fiches techniques, certificats, caractéristiques).	30.00	1	1	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 11:28:11	2025-12-26 11:28:11	\N
a0ae1204-1dda-4b7f-8186-217334a97501	a0ae0f46-357b-43e2-af5e-f2e422a91e41	CRIT-002	Prix et compétitivité de l’offre financière	Évalue le montant total de l’offre financière, la cohérence des prix unitaires, la compétitivité par rapport aux prix du marché et la clarté du devis détaillé.	30.00	1	1	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 20:34:30	2025-12-25 20:57:37	\N
a0ae12b2-98cb-470a-8315-a3bbb28e0a05	a0ae0f46-357b-43e2-af5e-f2e422a91e41	CRIT-004	Expérience et références du soumissionnaire	Évalue l’expérience du fournisseur dans la livraison de matériels de construction similaires, les références récentes, ainsi que la satisfaction des clients précédents.	10.00	1	3	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 20:36:24	2025-12-25 20:57:53	\N
a0af523b-851c-4f6b-be59-5a0bedc4b6ea	a0af507f-239c-4b4b-8598-34f54238aa16	CRIT-002	Prix et compétitivité de l’offre financière	Évalue le montant global de l’offre, la cohérence des prix unitaires, la compétitivité par rapport aux prix du marché et la clarté du devis.	70.00	1	2	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 11:29:53	2025-12-26 11:29:53	\N
a0b7690e-0b41-4601-a664-0d3916239c1b	a0b75169-b3a6-44b0-9765-e59fba89f945	CRIT-001	Conformité et Qualité Technique	Évaluation de la conformité des matériels proposés aux spécifications techniques du cahier des charges. Ce critère prend en compte la qualité des équipements (robustesse, durabilité, normes de fabrication), les caractéristiques techniques (puissance, capacité, rendement), la garantie offerte (durée et couverture), ainsi que le service après-vente (disponibilité des pièces de rechange, assistance technique, délai d'intervention).	45.00	1	1	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 12:00:19	2025-12-30 12:00:19	\N
a0b76983-99f7-4f5d-b1e0-6074a1527970	a0b75169-b3a6-44b0-9765-e59fba89f945	CRIT-002	Offre Financière et Conditions Commerciales	Évaluation du montant global de l'offre financière et des conditions commerciales proposées. Ce critère analyse la compétitivité des prix unitaires et totaux, les modalités de paiement proposées, les délais de livraison, ainsi que les éventuelles remises ou avantages commerciaux. La cohérence entre le prix et la qualité technique sera également examinée.	55.00	1	2	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 12:01:36	2025-12-30 12:01:36	\N
\.


--
-- TOC entry 5419 (class 0 OID 48635)
-- Dependencies: 237
-- Data for Name: documents; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.documents (id_document, lot_id, type_document, titre_document, fichier_nom_document, fichier_path_document, fichier_type_document, fichier_taille_document, description_document, date_document, version_document, est_valide_document, valide_par, valide_at, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0b99886-6008-49bb-9de0-abe5a153c219	a0b75ab3-080b-41d0-9eab-ea2f12b25190	autre	Titre du document	Bishop-Moude-S-e1747746199916-1024x623.jpg	documents/lots/a0b75ab3-080b-41d0-9eab-ea2f12b25190/bishop-moude-s-e1747746199916-1024x623_1767190311_iDZRDMyz.jpg	image/jpeg	0.28	Description	2025-12-31 00:00:00	2	f	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 14:04:44	2025-12-31 14:11:51	\N
\.


--
-- TOC entry 5418 (class 0 OID 48572)
-- Dependencies: 236
-- Data for Name: evaluations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.evaluations (id_evaluation, version, is_current, numero_evaluation, date_evaluation, resultat_evaluation, note_maximale, pourcentage_final, rang, respo_technique_evaluation, superviseur_evaluation, evalue_par, statut_evaluation, commentaire_general, recommandation, documents_evalues, evaluateur_principal_id, date_validation, motif_validation, valide_par, date_rejet, motif_rejet, rejete_par, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, evaluation_parent_id, attribution_id, critere_evaluation_id) FROM stdin;
a0ae6d6f-21a5-4243-b0b3-c475f8b823bb	1	t	EVAL-LOT-2025-AZ0025-CRIT-002-2025-0001	2025-12-26 00:50:07	30.00	30.00	100.00	1	{"nom_complet":"DAMBELE KONATE ALBERT","email":"albertdamb@gmail.com","telephone":"+2250785001241"}	{"nom_complet":"M. KOFFI ADOU RICHARD","email":"koffi.adou@gmail.com","telephone":"+2250101013321"}	{"nom_complet":"Mme. ALANGBA AHOU PAULINE","email":"pauline02alangba@gmail.com","telephone":"+2250320012012"}	3	L’offre financière est jugée très satisfaisante et présente un excellent niveau de compétitivité. Elle constitue une solution économiquement optimale pour le maître d’ouvrage et contribue de manière significative à l’optimisation des ressources financières du projet.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 01:37:46	sdsd	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 00:50:07	2025-12-26 01:37:46	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae1204-1dda-4b7f-8186-217334a97501
a0ae9245-773e-4533-bca7-c504fd5670df	1	t	EVAL-LOT-2025-AZ0025-CRIT-001-2025-0001	2025-12-26 02:33:07	15.00	30.00	50.00	1	{"nom_complet":"M DAMBELE KONATE ALBERT","email":"albertdamb@gmail.com","telephone":"+2250785001241"}	{"nom_complet":"M. KOFFI ADOU RICHARD","email":"koffi.adou@gmail.com","telephone":"+2250101013321"}	{"nom_complet":"Mme. ALANGBA AHOU PAULINE","email":"pauline02alangba@gmail.com","telephone":"+2250320012012"}	3	L’offre est techniquement acceptable mais présente des lacunes qui réduisent son niveau de conformité globale. Une amélioration de la documentation technique et une meilleure précision des caractéristiques des matériels proposés seraient nécessaires pour atteindre un niveau de conformité pleinement satisfaisant.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 02:48:35	Au regard de l’analyse technique, financière et administrative effectuée par la commission d’évaluation, l’offre du soumissionnaire a été jugée conforme aux exigences du dossier d’appel d’offres. Les matériels proposés répondent pleinement aux spécifications techniques requises et présentent des garanties suffisantes en matière de qualité, de conformité et de durabilité.\r\n\r\nPar ailleurs, l’offre financière est compétitive et économiquement avantageuse, tout en respectant les conditions contractuelles et les délais de livraison exigés. Le soumissionnaire dispose également des capacités techniques, logistiques et de l’expérience nécessaires pour assurer la bonne exécution des prestations.\r\n\r\nEn conséquence, la commission d’évaluation valide l’offre et recommande son attribution, sous réserve du respect des dispositions réglementaires et contractuelles en vigueur.	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:33:07	2025-12-26 02:48:35	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae1192-b819-4da5-b307-d0979c3ac137
a0afa499-91a8-4528-9d2e-29f2cb78d982	1	t	EVAL-LMC-2026-001-CRIT-002-2025-0005	2025-12-26 15:20:12	10.00	70.00	14.29	4	{"nom_complet":"TIZIE LUIS","email":null,"telephone":null}	{"nom_complet":"ISSA YAKOU","email":null,"telephone":null}	{"nom_complet":"YAPO MARIANE","email":null,"telephone":null}	3	L’offre est peu compétitive et pourrait représenter un risque économique pour le maître d’ouvrage si elle était retenue. Une révision ou clarification des prix est recommandée	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 15:22:31	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:20:12	2025-12-26 15:23:12	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af523b-851c-4f6b-be59-5a0bedc4b6ea
a0ae94b3-cd0c-4a03-9511-8e0c5b2e86f7	1	t	EVAL-LOT-2025-AZ0025-CRIT-001-2025-0002	2025-12-26 02:39:55	10.00	30.00	33.33	2	{"nom_complet":"M. COULIBALY AMINATOU","email":"coulb@gmail.com","telephone":"+2250101230010"}	{"nom_complet":"M. AKA AMICHA CLAUDE","email":null,"telephone":"+2250010210210"}	{"nom_complet":"N'GUESSAN LOUKOU MARIE CLEMENTINE","email":"marie.ngussean@gmail.com","telephone":"+2250100001020"}	3	L’offre est jugée techniquement conforme et acceptable. Elle répond de manière satisfaisante aux exigences techniques du marché et offre des garanties suffisantes quant à la qualité et à la durabilité des matériels proposés. Les réserves identifiées n’affectent pas de manière significative la bonne exécution du projet.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 02:51:18	dff	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:39:55	2025-12-26 02:51:18	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae1192-b819-4da5-b307-d0979c3ac137
a0ae9741-3378-467c-9ae6-054b46ff08c2	1	t	EVAL-LOT-2025-AZ0025-CRIT-001-2025-0003	2025-12-26 02:47:03	5.00	30.00	16.67	3	{"nom_complet":"M. GNAORE KADJA LAMBERT","email":"lambert.gnaore@gmail.com","telephone":"+2250585854511"}	{"nom_complet":"M. OUATTARA BAMOUSSA","email":"ouattarabamoussa@gmail.com","telephone":"+2251210121000"}	{"nom_complet":"M. DAGRI LEKIGNOUA PIERRE","email":"pierredagry01@gmail.com","telephone":"+2250202021200"}	3	L’offre est jugée techniquement excellente. Elle répond pleinement aux exigences du marché et offre des garanties solides en matière de qualité, de conformité et de fiabilité des matériels proposés. Aucun manquement technique n’a été relevé, ce qui justifie l’attribution de la note maximale.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 02:53:00	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:47:03	2025-12-26 02:53:00	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae1192-b819-4da5-b307-d0979c3ac137
a0ae9bc3-2465-4c4c-af9c-004cfe796cc0	1	t	EVAL-LOT-2025-AZ0025-CRIT-005-2025-0001	2025-12-26 02:59:40	5.00	5.00	100.00	1	{"nom_complet":"M. KAN JEAN LUIS","email":null,"telephone":null}	{"nom_complet":"M. MARCELIN KONE","email":null,"telephone":null}	{"nom_complet":"TOURE YAY","email":null,"telephone":null}	3	L’offre présente un excellent niveau de garanties et d’engagements contractuels. Le service après-livraison proposé apporte des assurances suffisantes quant à la fiabilité de la prestation et à la protection des intérêts du maître d’ouvrage. Ces éléments justifient pleinement l’attribution de la note maximale pour ce critère.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 03:00:45	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:59:40	2025-12-26 03:00:45	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae131a-6d1b-4a5e-a5ed-d845fc514104
a0aea233-92de-4a14-86da-76be74a32058	1	t	EVAL-LOT-2025-AZ0025-CRIT-006-2025-0001	2025-12-26 03:17:40	15.00	15.00	100.00	1	{"nom_complet":"M. COULIBALY AMINATOU","email":"coulb@gmail.com","telephone":"+2250101230010"}	{"nom_complet":"M. OUATTARA BAMOUSSA","email":"ouattarabamoussa@gmail.com","telephone":"+2251210121000"}	{"nom_complet":"M. DAGRI LEKIGNOUA PIERRE","email":"pierredagry01@gmail.com","telephone":"+2250202021200"}	3	L’offre présente un excellent niveau de performance en matière de délai et de planification de la livraison. L’organisation proposée garantit une exécution efficace et sécurisée des prestations, contribuant au bon déroulement global du projet. Ces éléments justifient l’attribution de la note maximale pour ce critère.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 03:19:13	À l’issue de l’analyse technique, financière et administrative, l’offre du soumissionnaire a été jugée conforme aux exigences du dossier d’appel d’offres relatif au lot « Livraison de matériels de construction ». Les matériels proposés répondent aux spécifications techniques requises et présentent des garanties satisfaisantes de qualité et de conformité.\r\n\r\nL’offre financière est compétitive et économiquement avantageuse, tandis que les délais et le planning de livraison proposés sont réalistes et compatibles avec les contraintes du projet. Le soumissionnaire justifie par ailleurs d’une expérience et de références pertinentes, ainsi que de capacités logistiques adéquates.\r\n\r\nEn conséquence, la commission d’évaluation valide l’offre et recommande son attribution conformément aux dispositions en vigueur.	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 03:17:40	2025-12-26 03:19:13	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae147c-080e-46b5-be1a-82cb41dd6374
a0ae9e5c-83dc-4b39-89ec-07f0a83c17e2	1	t	EVAL-LOT-2025-AZ0025-CRIT-004-2025-0001	2025-12-26 03:06:56	10.00	10.00	100.00	1	{"nom_complet":"N'THE THETIE ANNE","email":"ntheanne@gmail.com","telephone":"+2250101002023"}	{"nom_complet":"TRA BI IRIE","email":"biirietra@gmail.com","telephone":"+2250020001000"}	{"nom_complet":"OBITE AUGUSTIN","email":null,"telephone":null}	3	L’offre présente un excellent niveau d’expérience et de références. Le soumissionnaire dispose des compétences techniques et organisationnelles nécessaires pour mener à bien les prestations prévues, ce qui justifie pleinement l’attribution de la note maximale pour ce critère.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 03:07:30	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 03:06:56	2025-12-26 03:07:30	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae12b2-98cb-470a-8315-a3bbb28e0a05
a0aea4b6-ef68-48b2-a00b-9dae74032a03	1	t	EVAL-LOT-2025-AZ0025-CRIT-003-2025-0001	2025-12-26 03:24:42	10.00	10.00	100.00	1	{"nom_complet":"M. DAMBELE KONATE ALBERT","email":"albertdamb@gmail.com","telephone":"+2250785001241"}	{"nom_complet":"M. KOFFI ADOU RICHARD","email":"koffi.adou@gmail.com","telephone":"+2250101013321"}	{"nom_complet":"Mme. ALANGBA AHOU PAULINE","email":"pauline02alangba@gmail.com","telephone":"+2250320012012"}	3	L’offre présente un excellent niveau de capacité logistique et de moyens matériels. Le soumissionnaire est pleinement en mesure d’assurer la livraison sécurisée et ponctuelle des matériaux, ce qui justifie l’attribution de la note maximale pour ce critère.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 03:26:02	À l’issue de l’analyse technique, financière et administrative, l’offre du soumissionnaire a été jugée pleinement conforme aux exigences du dossier d’appel d’offres pour le lot « Livraison de matériels de construction ».\r\n\r\nTous les critères d’évaluation ont été satisfaits :\r\n\r\nConformité technique des matériels proposés : 30/30\r\n\r\nPrix et compétitivité de l’offre : 30/30\r\n\r\nDélai et planning de livraison : 15/15\r\n\r\nCapacité logistique et moyens matériels : 10/10\r\n\r\nExpérience et références : 10/10\r\n\r\nGaranties et service après-livraison : 5/5\r\n\r\nLes matériels proposés respectent les normes et spécifications techniques, le prix est compétitif, les délais réalistes et le soumissionnaire dispose des moyens logistiques, de l’expérience et des garanties nécessaires. L’offre est donc validée et son attribution recommandée.	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 03:24:42	2025-12-26 03:26:02	\N	\N	a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae1277-3d61-40b2-b013-8314f03f55c3
a0b7bd63-d9f5-412b-9e67-7553a8028277	1	t	EVAL-LOT-EPD-2025-CRIT-001-2025-0001	2025-12-30 15:56:08	50.00	50.00	100.00	1	{"nom_complet":"KOBENAN KAN","email":"kobenakan@gmail.com","telephone":null}	{"nom_complet":"AFFI KASSI","email":"affi@gmail.com","telephone":null}	{"nom_complet":"BLE AHOULOU","email":"bleahoulou@gmail.com","telephone":"+2250100121011"}	3	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-30 16:17:55	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 15:56:08	2025-12-30 16:17:55	\N	\N	a0b78fd6-4ecd-4bfa-ba55-0966d0180d9c	a0b78a31-cfa8-469f-b9fd-e49be228ade7
a0af9b6d-9d7a-4ab9-af85-3a6a4e8d9f1a	1	t	EVAL-LMC-2026-001-CRIT-001-2025-0002	2025-12-26 14:54:33	15.00	30.00	50.00	1	{"nom_complet":"KONE ABOLY","email":null,"telephone":null}	{"nom_complet":"KASSI KADJO PIERRE","email":null,"telephone":null}	{"nom_complet":"ADDY CHRISTIANE","email":null,"telephone":null}	3	L’offre est jugée techniquement conforme et satisfaisante. Elle présente des garanties suffisantes quant à la qualité, à la performance et à la durabilité des matériels proposés, permettant ainsi une exécution correcte des prestations prévues.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 14:55:53	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 14:54:33	2025-12-26 14:55:53	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af51a0-0987-4026-9881-e5bbdb740974
a0af9a45-5c85-4a87-899f-e0482c6bc3fe	1	t	EVAL-LMC-2026-001-CRIT-001-2025-0001	2025-12-26 14:51:19	15.00	30.00	50.00	2	{"nom_complet":"KOFFI YAO","email":null,"telephone":null}	{"nom_complet":"KACOU BAH","email":"bah@gmail.com","telephone":"+2250212142201"}	{"nom_complet":"KONE BAMOUSSI","email":"kone@gmail.com","telephone":"+2251400120021"}	3	L’offre est jugée techniquement conforme et satisfaisante. Elle présente des garanties suffisantes quant à la qualité, à la performance et à la durabilité des matériels proposés, permettant ainsi une exécution correcte des prestations prévues.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 14:55:14	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 14:51:19	2025-12-26 14:55:53	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af51a0-0987-4026-9881-e5bbdb740974
a0af9e40-1447-47a7-b54e-116b36b5e8a5	1	t	EVAL-LMC-2026-001-CRIT-002-2025-0001	2025-12-26 15:02:27	30.00	70.00	42.86	1	{"nom_complet":"DAN LUC","email":null,"telephone":null}	{"nom_complet":"LOBA PIERRE","email":null,"telephone":null}	{"nom_complet":"TRAORE KANE","email":null,"telephone":null}	3	L’offre financière est très satisfaisante et offre un excellent rapport qualité/prix. Elle constitue la solution économiquement optimale pour le projet.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 15:22:53	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:02:27	2025-12-26 15:22:53	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af523b-851c-4f6b-be59-5a0bedc4b6ea
a0af9f6d-3a50-4740-869f-c3e56641fa68	1	t	EVAL-LMC-2026-001-CRIT-002-2025-0003	2025-12-26 15:05:44	0.00	70.00	0.00	5	{"nom_complet":"AKISSI KAN","email":null,"telephone":null}	{"nom_complet":"LUI VAN","email":null,"telephone":null}	{"nom_complet":"KOFFI ISSA","email":null,"telephone":null}	3	L’offre est techniquement acceptable et économiquement correcte, mais moins avantageuse que d’autres soumissions. Des négociations pourraient améliorer le rapport qualité/prix.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 15:23:03	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:05:44	2025-12-26 15:23:12	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af523b-851c-4f6b-be59-5a0bedc4b6ea
a0af9eda-9c2b-4b25-9f43-78729f763e8b	1	t	EVAL-LMC-2026-001-CRIT-002-2025-0002	2025-12-26 15:04:08	15.00	70.00	21.43	2	{"nom_complet":"AMANI JEAN MARCK","email":null,"telephone":null}	{"nom_complet":"ALIKO DESIRE","email":null,"telephone":null}	{"nom_complet":"TANGUI AMICHA","email":null,"telephone":null}	3	L’offre est jugée satisfaisante sur le plan financier. Elle présente un bon rapport qualité/prix et peut être retenue sans risque pour le budget du projet.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 15:22:09	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:04:08	2025-12-26 15:22:53	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af523b-851c-4f6b-be59-5a0bedc4b6ea
a0afa3da-2bbe-4d07-932d-774abaf8a565	1	t	EVAL-LMC-2026-001-CRIT-002-2025-0004	2025-12-26 15:18:06	15.00	70.00	21.43	3	{"nom_complet":"KANAN BI IRIE","email":"biirie@mail.com","telephone":null}	{"nom_complet":"TRA BI MARCELIN","email":"trabi@gmail.com","telephone":null}	{"nom_complet":"TEH Melo","email":null,"telephone":null}	3	L’offre est techniquement acceptable et économiquement correcte, mais moins avantageuse que d’autres soumissions. Des négociations pourraient améliorer le rapport qualité/prix.	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	2025-12-26 15:23:12	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:18:07	2025-12-26 15:23:12	\N	\N	a0af67ef-65a2-468b-a3d4-234546270174	a0af523b-851c-4f6b-be59-5a0bedc4b6ea
\.


--
-- TOC entry 5427 (class 0 OID 48956)
-- Dependencies: 245
-- Data for Name: evaluations_lots_prestataires; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.evaluations_lots_prestataires (id_evaluation_critere, critere_evaluation_id, evaluation_id, prestataire_id, note_obtenue, note_reference, note_finale, pourcentage, conforme, observation, justification, documents_fournis, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0ae6d6f-4596-42b1-a319-0c058f6e243f	a0ae1204-1dda-4b7f-8186-217334a97501	a0ae6d6f-21a5-4243-b0b3-c475f8b823bb	a0ae078c-1897-48de-8e7a-867ef2d066d8	30.00	30.00	30.00	100.00	f	L’offre financière présentée est la plus compétitive parmi l’ensemble des soumissionnaires. Les prix unitaires et le montant global sont cohérents, détaillés et conformes aux prix du marché pour des prestations similaires. Aucun écart significatif ou incohérence n’a été relevé dans le bordereau des prix.	Le soumissionnaire propose un coût global inférieur ou équivalent aux estimations du maître d’ouvrage, tout en respectant l’ensemble des exigences techniques et contractuelles. Les prix sont justifiés, réalistes et économiquement avantageux, garantissant un bon rapport qualité/prix sans compromettre la qualité des matériels à livrer.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 00:50:07	2025-12-26 00:50:07	\N
a0ae9245-a538-4952-b53b-b139520d3f1c	a0ae1192-b819-4da5-b307-d0979c3ac137	a0ae9245-773e-4533-bca7-c504fd5670df	a0ae078c-1897-48de-8e7a-867ef2d066d8	15.00	30.00	15.00	50.00	f	Les matériels proposés répondent partiellement aux spécifications techniques exigées dans le dossier d’appel d’offres. Certaines caractéristiques techniques sont conformes, cependant des insuffisances ont été relevées au niveau de la documentation technique et de la précision de certaines caractéristiques des matériaux proposés.	Bien que les matériaux principaux respectent les exigences minimales en termes de nature et d’usage, l’offre ne fournit pas l’ensemble des fiches techniques, certificats de conformité ou détails requis pour attester pleinement du respect des normes en vigueur. Ces manquements limitent l’appréciation complète de la qualité et de la conformité des matériels.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:33:07	2025-12-26 02:33:07	\N
a0ae94b3-d8fe-4859-a348-0969a4d9b8db	a0ae1192-b819-4da5-b307-d0979c3ac137	a0ae94b3-cd0c-4a03-9511-8e0c5b2e86f7	a0ae078c-1897-48de-8e7a-867ef2d066d8	10.00	30.00	10.00	33.33	f	Les matériels proposés sont globalement conformes aux spécifications techniques du dossier d’appel d’offres. Les principales caractéristiques techniques exigées sont respectées et les matériaux proposés répondent aux besoins du projet. Des compléments d’informations techniques ont permis de lever certaines insuffisances initialement relevées.	Les documents techniques fournis (fiches techniques, descriptions détaillées et références produits) permettent de confirmer la conformité des matériaux aux normes requises. Bien que quelques éléments mineurs puissent être améliorés, l’offre présente un niveau de conformité satisfaisant et techniquement fiable pour l’exécution des prestations prévues.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:39:55	2025-12-26 02:39:55	\N
a0ae9741-3ae8-4876-9278-2a31316fc27a	a0ae1192-b819-4da5-b307-d0979c3ac137	a0ae9741-3378-467c-9ae6-054b46ff08c2	a0ae078c-1897-48de-8e7a-867ef2d066d8	5.00	30.00	5.00	16.67	f	Les matériels proposés sont entièrement conformes aux spécifications techniques du dossier d’appel d’offres. L’ensemble des caractéristiques techniques exigées est respecté et les matériaux présentés répondent parfaitement aux besoins du projet. La documentation technique fournie est complète, claire et conforme aux normes en vigueur.	Les fiches techniques, certificats de conformité et descriptions détaillées fournis attestent du respect intégral des exigences techniques et normatives. Les matériaux proposés présentent des performances adaptées, une qualité satisfaisante et une durabilité conforme aux standards requis pour les travaux envisagés.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:47:03	2025-12-26 02:47:03	\N
a0ae9bc3-36d3-4e01-a9fc-1d38cc32eab7	a0ae131a-6d1b-4a5e-a5ed-d845fc514104	a0ae9bc3-2465-4c4c-af9c-004cfe796cc0	a0ae078c-1897-48de-8e7a-867ef2d066d8	5.00	5.00	5.00	100.00	f	Le soumissionnaire propose des garanties complètes et clairement définies sur l’ensemble des matériels livrés. Les engagements en matière de service après-livraison sont précis, réalistes et adaptés aux exigences du marché, notamment en ce qui concerne le remplacement des matériels non conformes ou défectueux.	Les garanties offertes couvrent la conformité, la qualité et la durabilité des matériels fournis. Le soumissionnaire s’engage formellement à assurer un remplacement rapide de tout matériel non conforme, sans incidence financière pour le maître d’ouvrage. Les modalités du service après-livraison sont clairement décrites et assorties de délais d’intervention satisfaisants.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 02:59:40	2025-12-26 02:59:40	\N
a0ae9e5c-8a3c-458f-b5fe-75bf07484b52	a0ae12b2-98cb-470a-8315-a3bbb28e0a05	a0ae9e5c-83dc-4b39-89ec-07f0a83c17e2	a0ae078c-1897-48de-8e7a-867ef2d066d8	10.00	10.00	10.00	100.00	f	\N	Les attestations de bonne exécution, contrats antérieurs et références clients fournis démontrent la capacité du soumissionnaire à exécuter des marchés comparables dans le respect des exigences techniques, des délais et des conditions contractuelles. L’expérience acquise constitue un gage de fiabilité et de maîtrise des prestations attendues.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 03:06:56	2025-12-26 03:06:56	\N
a0aea233-b2fe-498c-b546-edf5b6c13645	a0ae147c-080e-46b5-be1a-82cb41dd6374	a0aea233-92de-4a14-86da-76be74a32058	a0ae078c-1897-48de-8e7a-867ef2d066d8	15.00	15.00	15.00	100.00	f	Le soumissionnaire propose un délai de livraison court, réaliste et parfaitement adapté aux exigences du projet. Le planning de livraison présenté est clair, détaillé et cohérent, avec une organisation précise des différentes étapes d’approvisionnement et de livraison.	Les délais proposés respectent strictement les exigences du dossier d’appel d’offres et tiennent compte des contraintes logistiques et opérationnelles du site. Le planning fourni démontre une bonne maîtrise des flux d’approvisionnement et une capacité effective à assurer des livraisons régulières et ponctuelles, limitant ainsi tout risque de retard.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 03:17:40	2025-12-26 03:17:40	\N
a0aea4b6-fae5-413e-af6f-e1946d16a80b	a0ae1277-3d61-40b2-b013-8314f03f55c3	a0aea4b6-ef68-48b2-a00b-9dae74032a03	a0ae078c-1897-48de-8e7a-867ef2d066d8	10.00	10.00	10.00	100.00	f	Le soumissionnaire dispose de moyens logistiques et matériels adaptés pour assurer la livraison efficace des matériels de construction. Les véhicules, équipements de manutention et moyens d’entreposage proposés sont suffisants et conformes aux exigences du projet.	Les informations fournies démontrent que le soumissionnaire peut mobiliser les ressources nécessaires pour respecter les délais et garantir l’intégrité des matériels lors du transport et du déchargement. La planification logistique est réaliste et cohérente avec les besoins du projet.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 03:24:42	2025-12-26 03:24:42	\N
a0af9a45-9a26-4524-afe8-b897905b0cfe	a0af51a0-0987-4026-9881-e5bbdb740974	a0af9a45-5c85-4a87-899f-e0482c6bc3fe	a0ae078c-1897-48de-8e7a-867ef2d066d8	15.00	30.00	15.00	50.00	f	Les matériels proposés par le soumissionnaire sont conformes aux spécifications techniques définies dans le dossier d’appel d’offres. Les caractéristiques techniques essentielles exigées sont respectées et les matériaux présentés répondent aux besoins du projet.	Les fiches techniques, descriptions détaillées et, le cas échéant, les certificats de conformité fournis permettent de vérifier que les matériels proposés respectent les normes techniques et qualitatives en vigueur. Aucun écart majeur n’a été constaté entre les spécifications requises et les éléments proposés.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 14:51:19	2025-12-26 14:51:19	\N
a0af9b6d-afb0-46aa-bb61-6e6d31286579	a0af51a0-0987-4026-9881-e5bbdb740974	a0af9b6d-9d7a-4ab9-af85-3a6a4e8d9f1a	a0ae078c-1897-48de-8e7a-867ef2d066d8	15.00	30.00	15.00	50.00	f	Les matériels proposés par le soumissionnaire sont conformes aux spécifications techniques définies dans le dossier d’appel d’offres. Les caractéristiques techniques essentielles exigées sont respectées et les matériaux présentés répondent aux besoins du projet.	Les fiches techniques, descriptions détaillées et, le cas échéant, les certificats de conformité fournis permettent de vérifier que les matériels proposés respectent les normes techniques et qualitatives en vigueur. Aucun écart majeur n’a été constaté entre les spécifications requises et les éléments proposés.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 14:54:33	2025-12-26 14:54:33	\N
a0af9e40-3814-4087-b051-4e746e4d2146	a0af523b-851c-4f6b-be59-5a0bedc4b6ea	a0af9e40-1447-47a7-b54e-116b36b5e8a5	a0ae078c-1897-48de-8e7a-867ef2d066d8	30.00	70.00	30.00	42.86	f	L’offre financière est la plus compétitive parmi toutes les soumissions. Les prix unitaires et le montant global sont cohérents, détaillés et conformes aux prix du marché.	Le soumissionnaire propose un coût global inférieur ou équivalent aux estimations du maître d’ouvrage, tout en respectant l’ensemble des exigences techniques et contractuelles.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:02:27	2025-12-26 15:02:27	\N
a0af9eda-a35b-4035-963b-1307b6b472f6	a0af523b-851c-4f6b-be59-5a0bedc4b6ea	a0af9eda-9c2b-4b25-9f43-78729f763e8b	a0ae078c-1897-48de-8e7a-867ef2d066d8	15.00	70.00	15.00	21.43	f	L’offre financière est compétitive et raisonnable, avec un montant global légèrement supérieur à l’offre la plus basse, mais restant conforme aux prix du marché.	Le soumissionnaire présente des prix réalistes et justifiés. Les écarts avec les meilleures offres ne compromettent pas l’équilibre financier du projet.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:04:08	2025-12-26 15:04:08	\N
a0af9f6d-4fe9-43e3-ab38-59bb533a1a8a	a0af523b-851c-4f6b-be59-5a0bedc4b6ea	a0af9f6d-3a50-4740-869f-c3e56641fa68	a0ae078c-1897-48de-8e7a-867ef2d066d8	0.00	70.00	0.00	0.00	f	L’offre financière est acceptable, mais certains prix unitaires apparaissent légèrement élevés par rapport aux références du marché.	Les écarts de prix sont justifiés par la qualité ou les garanties proposées, mais l’offre n’est pas la plus compétitive.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:05:44	2025-12-26 15:05:44	\N
a0afa3da-5984-4f50-a9e5-ba59f65e6713	a0af523b-851c-4f6b-be59-5a0bedc4b6ea	a0afa3da-2bbe-4d07-932d-774abaf8a565	a0ae078c-1897-48de-8e7a-867ef2d066d8	15.00	70.00	15.00	21.43	f	L’offre financière est acceptable, mais certains prix unitaires apparaissent légèrement élevés par rapport aux références du marché.	Les écarts de prix sont justifiés par la qualité ou les garanties proposées, mais l’offre n’est pas la plus compétitive.	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:18:07	2025-12-26 15:18:07	\N
a0afa499-a07e-44d9-ae30-004392d4d518	a0af523b-851c-4f6b-be59-5a0bedc4b6ea	a0afa499-91a8-4528-9d2e-29f2cb78d982	a0ae078c-1897-48de-8e7a-867ef2d066d8	10.00	70.00	10.00	14.29	f	L’offre financière présente un coût global élevé ou des incohérences dans le détail des prix unitaires.	Certains postes sont surévalués ou non justifiés, réduisant la compétitivité de l’offre par rapport aux	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:20:12	2025-12-26 15:20:12	\N
a0b7bd63-f7cd-4860-950d-2e70bfb6f206	a0b78a31-cfa8-469f-b9fd-e49be228ade7	a0b7bd63-d9f5-412b-9e67-7553a8028277	a0af6c59-ef06-4bf7-97ef-fec58547831c	50.00	50.00	50.00	100.00	f	Très bien	Travail bien fait	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 15:56:09	2025-12-30 15:56:09	\N
\.


--
-- TOC entry 5423 (class 0 OID 48765)
-- Dependencies: 241
-- Data for Name: evaluations_prestataires; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.evaluations_prestataires (id_evaluation_prestataire, prestataire_id, note_qualification_evaluation_prestataire, date_derniere_evaluation_evaluation_prestataire, nombre_contrats_executes_evaluation_prestataire, taux_respect_delais_evaluation_prestataire, taux_qualite_evaluation_prestataire, nombre_litiges_evaluation_prestataire, liste_statut_evaluation_prestataire, date_mise_en_liste_evaluation_prestataire, date_fin_sanction_evaluation_prestataire, motif_liste_noire_evaluation_prestataire, commentaire_evaluation_prestataire, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5424 (class 0 OID 48797)
-- Dependencies: 242
-- Data for Name: factures; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.factures (id_facture, proforma_id, numero_facture, montant_facture, date_facture, date_reception_facture, statut_facture, comment_facture, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0af3c84-842c-4de1-ba2b-6babad8c8d28	a0ae6915-de64-4c5b-acd2-df2742e3bdcc	FACT-85REJDFD58DF5	192093000.00	2025-12-26	2025-12-26	partiellement_payee	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 10:29:11	2025-12-26 10:42:01	\N
a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0b794c1-aa70-4771-bf98-1a57e0c854ff	FACT-REFR25U012-451L	15000000.00	2025-12-30	2025-12-30	payee	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 16:49:01	2025-12-30 17:36:57	\N
a0afad02-02c7-4eb0-abea-f0699eaf505d	a0af67ef-39bd-4b60-aab7-f7f55dabd04e	FACT-250MK54452	100300000.00	2025-12-26	2025-12-26	payee	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:43:43	2025-12-31 07:25:22	\N
\.


--
-- TOC entry 5407 (class 0 OID 48160)
-- Dependencies: 224
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5415 (class 0 OID 48449)
-- Dependencies: 233
-- Data for Name: lots; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lots (id_lot, appel_offre_id, numero, libelle, description_critere, specifications_techniques, motif_retrait, version_lot, date_attribution, date_debut_prevue, date_fin_prevue, date_retrait, attribution_lot, statut_lot, taux_penalites, statut_retrait, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id) FROM stdin;
a0ae0f46-357b-43e2-af5e-f2e422a91e41	a0ad6fd8-b11a-4eef-98d6-6a74a8e9d54d	LOT-2025-AZ0025	LIVRAISON DE MATERIELS DE CONSTRUCTION	Le présent marché porte sur la livraison de matériels et matériaux de construction destinés à la réalisation de travaux de bâtiment et d’infrastructures.\r\nLa prestation comprend l’approvisionnement, le transport, la livraison sur site, ainsi que le déchargement des matériels conformément aux besoins exprimés par le maître d’ouvrage.\r\n\r\nLes matériels fournis devront être neufs, de qualité supérieure, conformes aux normes nationales et internationales en vigueur, et adaptés aux conditions climatiques locales.\r\nLa livraison devra être effectuée dans les délais contractuels, avec un conditionnement garantissant la protection et l’intégrité des produits jusqu’à leur réception définitive.	1. Nature des matériels à livrer (à titre indicatif)\r\nCiment (CPJ, CEM II ou équivalent)\r\nSable (lavé, propre et sans impuretés)\r\nGravier (différentes granulométries selon besoins)\r\nFer à béton (HA Ø6, Ø8, Ø10, Ø12, Ø14, Ø16, etc.)\r\nBriques, parpaings ou blocs creux normalisés\r\nBois de coffrage traité\r\nTôles de couverture (selon spécifications)\r\nAutres matériels de construction selon le bordereau des quantités\r\n\r\n2. Exigences de qualité\r\nTous les matériaux doivent être neufs, non utilisés\r\nConformité aux normes techniques en vigueur (ISO, NF, normes nationales)\r\nRésistance mécanique et durabilité adaptées aux travaux prévus\r\nCertificats de conformité ou fiches techniques fournis sur demande\r\n\r\n3. Conditions de livraison\r\nLivraison effectuée sur le site désigné par le maître d’ouvrage\r\nRespect strict du calendrier de livraison\r\nMatériels livrés en quantités exactes et en bon état\r\nDéchargement à la charge du fournisseur\r\nÉtablissement d’un procès-verbal de réception après contrôle\r\n\r\n4. Transport et sécurité\r\nMoyens de transport adaptés au type de matériaux\r\nProtection contre les intempéries et chocs durant le transport\r\nRespect des règles de sécurité et de manutention\r\n\r\n5. Garanties\r\nGarantie de conformité des matériels livrés\r\nRemplacement immédiat de tout matériel non conforme ou défectueux\r\nResponsabilité du fournisseur engagée jusqu’à la réception définitive	\N	1	2025-12-26	2025-12-26 00:00:00	2025-12-28 00:00:00	\N	1	1	5.50	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 20:26:50	2025-12-26 00:37:58	\N	\N
a0af507f-239c-4b4b-8598-34f54238aa16	a0af4a2a-5f0b-4464-9cf6-d564b102c40f	LMC-2026-001	LIVRAISON DE MATERIELS DE CONSTRUCTION	Le présent marché porte sur la livraison de matériels et matériaux de construction destinés à la réalisation de travaux de bâtiment et d’infrastructures. La prestation comprend l’approvisionnement, le transport, la livraison sur site et le déchargement des matériaux conformément aux besoins exprimés par le maître d’ouvrage.\r\n\r\nLes matériels fournis devront être neufs, de qualité conforme aux normes en vigueur, adaptés aux conditions d’utilisation prévues et livrés dans les délais contractuels. Le fournisseur est tenu de garantir l’intégrité des matériaux jusqu’à leur réception définitive et de remplacer tout matériel non conforme ou défectueux.\r\n\r\nLa livraison devra s’effectuer selon un planning préalablement validé, dans le respect des règles de sécurité et de manutention, afin d’assurer la bonne exécution des travaux auxquels les matériels sont destinés.	Les matériels et matériaux de construction à livrer devront répondre strictement aux exigences techniques ci-après :\r\n\r\n1. Nature des matériels\r\n\r\nCiment (CPJ, CEM II ou équivalent) conforme aux normes en vigueur\r\n\r\nSable propre, lavé, exempt d’argile et de matières organiques\r\n\r\nGravier concassé ou roulé, de granulométrie conforme aux prescriptions techniques\r\n\r\nFer à béton (HA Ø6, Ø8, Ø10, Ø12, Ø14, Ø16 et plus selon besoins), conforme aux normes de résistance\r\nBriques, parpaings ou blocs creux normalisés\r\nBois de coffrage sec et traité\r\nTôles de couverture ou matériaux de toiture conformes aux spécifications\r\nAutres matériaux selon le bordereau des quantités	\N	1	2025-12-26	2025-12-30 00:00:00	2026-01-26 00:00:00	\N	1	1	2.50	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 11:25:02	2025-12-26 12:30:34	\N	\N
a0b75944-eb3a-429b-a4bc-68670f6385c7	a0b5cc10-5576-4f4d-b604-f15713576d6e	LOT-REE-2025	RAVITAILLEMENT EN EAU	Le ravitaillement en eau consiste à assurer l’approvisionnement régulier, fiable et sécurisé en eau potable ou en eau à usage domestique et agricole au profit des populations, des établissements publics ou des exploitations agricoles.\r\nCe système vise à répondre aux besoins essentiels en eau pour la consommation humaine, l’hygiène, l’irrigation légère et les activités communautaires, notamment dans les zones rurales ou insuffisamment desservies par les réseaux publics.\r\n\r\nLe dispositif de ravitaillement peut inclure le captage, le stockage, le transport et la distribution de l’eau, tout en respectant les normes de qualité, de sécurité et de durabilité environnementale.	🔹 Source d’Eau\r\n\r\nType : Forage, puits amélioré, réseau public ou eau de surface traitée\r\n\r\nProfondeur du forage (si applicable) : 30 à 80 m\r\n\r\nDébit minimal requis : 2 à 10 m³/heure\r\n\r\n🔹 Équipement de Pompage\r\n\r\nType de pompe :\r\n\r\nPompe immergée électrique ou solaire\r\n\r\nPompe thermique (diesel ou essence)\r\n\r\nPuissance : 1 à 5 HP (selon débit)\r\n\r\nDébit nominal : 2 000 à 10 000 L/h\r\n\r\nMatériau : Inox ou fonte traitée anticorrosion\r\n\r\n🔹 Stockage de l’Eau\r\n\r\nType de réservoir : Cuve plastique alimentaire ou château d’eau métallique\r\n\r\nCapacité : 2 000 à 50 000 litres\r\n\r\nRésistance : UV, chaleur, intempéries\r\n\r\nSupport : Socle béton armé ou structure métallique\r\n\r\n🔹 Réseau de Distribution\r\n\r\nTuyauterie : PVC pression ou PEHD\r\n\r\nDiamètre nominal : 32 à 90 mm\r\n\r\nPression de service : 6 à 10 bars\r\n\r\nPoints de puisage : Bornes-fontaines, robinets, abreuvoirs\r\n\r\n🔹 Traitement & Qualité de l’Eau\r\n\r\nSystème de filtration : Sable, cartouche ou filtre à tamis\r\n\r\nDésinfection : Chloration manuelle ou automatique\r\n\r\nConformité : Normes OMS pour l’eau potable\r\n\r\n🔹 Alimentation Énergétique\r\n\r\nÉlectricité réseau ou groupe électrogène\r\n\r\nOption solaire :\r\n\r\nPanneaux solaires : 500 à 3 000 W\r\n\r\nRégulateur et batteries adaptées\r\n\r\nAutonomie minimale : 8 à 12 heures/jour\r\n\r\n🔹 Sécurité & Exploitation\r\n\r\nClapets anti-retour\r\n\r\nVannes de contrôle\r\n\r\nCoffret de commande sécurisé\r\n\r\nManuel d’exploitation et de maintenance\r\n\r\nAvantages du Système\r\n\r\n✔ Approvisionnement continu en eau\r\n✔ Amélioration des conditions sanitaires\r\n✔ Réduction des maladies hydriques\r\n✔ Solution adaptée aux zones rurales et périurbaines\r\n✔ Maintenance simple et durable	\N	1	\N	2025-12-31 00:00:00	2026-01-01 00:00:00	\N	0	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 11:16:11	2025-12-30 11:16:11	\N	\N
a0b75169-b3a6-44b0-9765-e59fba89f945	a0b5cc10-5576-4f4d-b604-f15713576d6e	LOT-AMA-2025	ACHAT DE METERIELS AGRICOLE	🚜 Mini-Tracteur Agricole – Description\r\n\r\nLe mini-tracteur est un tracteur agricole de petite taille, polyvalent et maniable, conçu pour effectuer diverses tâches agricoles telles que le labour, le hersage, l’andainage, le semis, le transport de charges, l’entretien des cultures et des allées. Adapté aux exploitations maraîchères, aux vergers, aux petites fermes, aux terrains irréguliers ou restreints, il offre un excellent compromis entre puissance, consommation et coût d’entretien.\r\n\r\nCe type de tracteur peut être équipé de divers outils adaptables : charrue, fraise rotative, porte-outils, remorque, broyeur, bineuse, etc.	🔧 Moteur\r\nType : Diesel 3 cylindres, refroidissement liquide\r\nPuissance nette : 20 à 35 CV (chevaux)\r\nVitesse maximale : ~25–30 km/h\r\nDémarrage : Électrique\r\n\r\n🔄 Transmission\r\nType : Manuel ou synchronisé\r\nVitesses avant : 8 à 12\r\nVitesses arrière : 2 à 4\r\nEmbrayage : Sec à friction\r\n\r\n🚜 Essieu & Direction\r\nDirection : Assistée / mécanique (selon version)\r\nFreins : À disque à bain d’huile\r\nDifférentiel : Blocable\r\n\r\n⚙️ Système Hydraulique\r\nPompe hydraulique : Débit moyen ~15–25 L/min\r\nCatégorie 1 attelage 3 points\r\nCapacité de levage : 500 à 1200 kg\r\n\r\nPrise de force (PDF) :\r\nRégime : 540 et/ou 1000 tr/min\r\nEmbrayage de PDF indépendant\r\n\r\n🛞 Dimensions & Poids\r\nPoids en ordre de marche : 800 à 1500 kg\r\nEmpattement : ~1500–1800 mm\r\nGarde au sol : ~300 mm\r\n\r\nPneus :\r\nAvant : 6.00–12\r\nArrière : 9.5–24\r\n\r\n⛽ Capacités & Performances\r\nRéservoir carburant : 30–50 L\r\nConsommation moyenne : 2–4 L/h (selon charge)\r\nAutorisation de charge à l’attelage : selon modèle\r\n\r\n💺 Confort & Sécurité\r\nSiège réglable\r\nCeinture de sécurité\r\nProtection ROPS (barre anti-renversement)\r\nÉclairage avant/arrière\r\nTableau de bord avec indicateurs essentiels\r\n\r\n🧩 Accessoires & Outils Compatibles\r\nVoici des outils fréquemment utilisés avec un mini-tracteur :\r\nFraise rotative — pour préparer le sol\r\nCharrue réversible — pour labourer\r\nHerse ou herse rotative — pour émietter\r\nRemorque agricole — pour le transport\r\nBroyeur de végétation — entretien des broussailles\r\nPlanteuse / semoir — pour cultures spécifiques\r\n\r\n🛒 Pourquoi Choisir Ce Type de Matériel\r\n✔ Polyvalent et adapté aux petites exploitations\r\n✔ Coût d’achat et d’entretien raisonnable\r\n✔ Facile à manœuvrer même en terrains étroits\r\n✔ Compatible avec plusieurs outils agricoles\r\n✔ Bon rendement pour travaux saisonniers	\N	1	2025-12-30	2025-12-31 00:00:00	2026-01-01 00:00:00	\N	1	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 10:54:15	2025-12-30 13:20:31	\N	\N
a0b75bf3-b691-4e2d-95c0-a519d84916ee	a0b5cc10-5576-4f4d-b604-f15713576d6e	LOT-MR-2025	Mobilisation des Ressources	La mobilisation des ressources vise à identifier, planifier et sécuriser l’ensemble des moyens nécessaires à la mise en œuvre efficace du projet.\r\nElle concerne aussi bien les ressources financières, humaines, matérielles que logistiques, afin d’assurer une exécution conforme aux objectifs, aux délais et aux exigences de qualité du projet. Cette étape garantit la disponibilité des intrants essentiels et la coordination optimale des parties prenantes.	Mobilisation des ressources financières\r\n\r\nIdentification des sources de financement (fonds propres, subventions, partenaires techniques et financiers)\r\n\r\nÉlaboration du budget prévisionnel détaillé\r\n\r\nPlanification des décaissements selon le calendrier du projet\r\n\r\nMobilisation des ressources humaines\r\n\r\nDéfinition des profils techniques requis (ingénieurs, techniciens, ouvriers spécialisés)\r\n\r\nRecrutement et affectation du personnel\r\n\r\nOrganisation des équipes et répartition des responsabilités\r\n\r\nMobilisation des ressources matérielles\r\n\r\nIdentification et acquisition des équipements, matériels et intrants nécessaires\r\n\r\nApprovisionnement en matériels agricoles, hydrauliques et de chantier\r\n\r\nContrôle de la conformité et de la qualité des équipements\r\n\r\nMobilisation logistique\r\n\r\nOrganisation du transport, du stockage et de la manutention\r\n\r\nMise en place des bases de chantier et des zones de stockage\r\n\r\nGestion des délais d’approvisionnement\r\n\r\nCoordination et partenariats\r\n\r\nCollaboration avec les autorités locales et services techniques\r\n\r\nImplication des bénéficiaires et parties prenantes\r\n\r\nMise en place des mécanismes de suivi des ressources mobilisées\r\n\r\nLivrables attendus\r\n\r\nPlan de mobilisation des ressources\r\n\r\nBudget détaillé et plan de financement\r\n\r\nPlanning d’affectation des ressources\r\n\r\nRapports de suivi de la mobilisation	\N	1	\N	2025-12-31 00:00:00	2026-01-02 00:00:00	\N	0	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 11:23:41	2025-12-30 11:23:41	\N	\N
a0b75ab3-080b-41d0-9eab-ea2f12b25190	a0b5cc10-5576-4f4d-b604-f15713576d6e	LOT-EPD-2025	Études Préliminaires et Diagnostic	Les études préliminaires et le diagnostic constituent la phase initiale du projet. Elles visent à analyser la situation existante afin d’identifier les besoins réels, les contraintes techniques, environnementales, socio-économiques et institutionnelles, ainsi que les opportunités de développement.\r\nCette étape permet de collecter et d’analyser les données de base nécessaires à la conception d’un projet pertinent, durable et techniquement faisable. Elle aboutit à un diagnostic détaillé servant de fondement aux choix techniques, économiques et organisationnels du projet.	Collecte de données\r\n\r\nDonnées physiques et environnementales (climat, sols, ressources en eau, topographie)\r\n\r\nDonnées socio-économiques (population cible, activités agricoles, pratiques existantes)\r\n\r\nDonnées institutionnelles et réglementaires applicables\r\n\r\nAnalyses techniques\r\n\r\nÉtat des infrastructures existantes (forages, réseaux d’irrigation, équipements agricoles, etc.)\r\n\r\nAnalyse des capacités de production et des contraintes techniques\r\n\r\nIdentification des risques techniques et environnementaux\r\n\r\nÉtudes spécifiques (selon le projet)\r\n\r\nÉtude hydrologique et hydrogéologique\r\n\r\nÉtude agronomique (types de cultures, rendements, besoins en eau)\r\n\r\nAnalyse environnementale et sociale préliminaire\r\n\r\nDiagnostic global\r\n\r\nIdentification des besoins prioritaires\r\n\r\nDéfinition des problèmes majeurs et de leurs causes\r\n\r\nÉvaluation des potentialités et des limites du site\r\n\r\nLivrables attendus\r\n\r\nRapport d’études préliminaires et de diagnostic\r\n\r\nCartes thématiques et schémas explicatifs\r\n\r\nRecommandations techniques pour la phase de conception du projet	\N	1	2025-12-30	2025-12-31 00:00:00	2026-01-02 00:00:00	\N	1	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 11:20:11	2025-12-30 13:48:46	\N	\N
a0b75b2a-ae5b-4e68-90a5-9a8902d80cac	a0b5cc10-5576-4f4d-b604-f15713576d6e	LOT-CTP-2025	Conception Technique du Projet	La conception technique du projet consiste à traduire les résultats des études préliminaires et du diagnostic en solutions techniques concrètes, adaptées aux besoins identifiés.\r\nElle vise à définir l’ensemble des ouvrages, équipements et systèmes nécessaires à la mise en œuvre du projet, en tenant compte des contraintes techniques, économiques, environnementales et réglementaires, afin de garantir la faisabilité, la durabilité et la performance du projet.	Définition des solutions techniques\r\n\r\nChoix des technologies adaptées (forage, pompage, stockage, irrigation, équipements agricoles)\r\n\r\nDimensionnement des infrastructures selon les besoins réels et les normes en vigueur\r\n\r\nSélection des matériaux et équipements conformes aux standards de qualité\r\n\r\nÉtudes et calculs techniques\r\n\r\nCalculs hydrauliques (débits, pressions, volumes de stockage)\r\n\r\nDimensionnement des réseaux (adduction, distribution, irrigation)\r\n\r\nÉtudes énergétiques (pompes électriques, solaires ou thermiques)\r\n\r\nConception des ouvrages\r\n\r\nPlans détaillés des ouvrages (forages, châteaux d’eau, bassins, réseaux)\r\n\r\nSchémas de principe et plans d’exécution\r\n\r\nIntégration des dispositifs de sécurité et de protection\r\n\r\nPrise en compte des aspects environnementaux et sociaux\r\n\r\nMesures de protection de l’environnement\r\n\r\nGestion durable des ressources en eau\r\n\r\nAdaptation aux usages locaux et aux capacités de maintenance\r\n\r\nEstimation technique et financière\r\n\r\nQuantitatif des travaux et équipements\r\n\r\nEstimation des coûts de réalisation\r\n\r\nAnalyse des options techniques et optimisation des coûts\r\n\r\nLivrables attendus\r\n\r\nDossier de conception technique (plans, notes de calcul, schémas)\r\n\r\nCahier des spécifications techniques\r\n\r\nAvant-projet sommaire (APS) et avant-projet détaillé (APD)\r\n\r\nRecommandations pour la phase d’exécution	\N	1	2025-12-30	2025-12-31 00:00:00	2026-01-02 00:00:00	\N	1	1	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 11:21:29	2025-12-30 14:02:31	\N	\N
\.


--
-- TOC entry 5403 (class 0 OID 48086)
-- Dependencies: 220
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_11_000000_create_roles_table	1
2	2014_10_12_000000_create_users_table	1
3	2014_10_12_100000_create_password_reset_tokens_table	1
4	2019_08_19_000000_create_failed_jobs_table	1
5	2019_12_14_000001_create_personal_access_tokens_table	1
6	2025_11_20_114959_create_permissions_table	1
7	2025_11_20_115028_create_role_permissions_table	1
8	2025_11_20_122235_create_type_appel_offres_table	1
9	2025_11_20_122355_create_appels_offres_table	1
10	2025_11_20_122424_create_caracteristique_appel_offres_table	1
11	2025_11_20_122457_create_proformas_table	1
12	2025_11_20_122459_create_lots_table	1
13	2025_11_20_122612_create_critere_evaluations_table	1
14	2025_11_20_151611_create_prestataires_table	1
15	2025_11_20_151612_create_evaluations_table	1
16	2025_11_20_151640_create_documents_table	1
17	2025_11_20_151714_create_banques_table	1
18	2025_11_20_151753_create_capacite_techniques_table	1
19	2025_11_20_151819_create_situation_financieres_table	1
20	2025_11_20_151900_create_evaluation_prestataires_table	1
21	2025_11_20_152740_create_factures_table	1
22	2025_11_20_152740_create_paiements_table	1
23	2025_11_20_152919_create_prestataires_lots_table	1
24	2025_11_20_152947_create_evaluations_lots_prestataires_table	1
25	2025_11_20_153204_create_alertes_table	1
26	2025_12_08_153200_creat_fixe_colonnes_table	1
27	2025_12_16_151847_add_critere_evaluation_id_to_evaluations_table	1
\.


--
-- TOC entry 5425 (class 0 OID 48837)
-- Dependencies: 243
-- Data for Name: paiements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.paiements (id_paiement, facture_id, banque_id, montant_net_paye_paiement, statut_paiement, date_validation_paiement, motif_rejet_paiement, observations_paiement, valide_par, paye_par, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, date_effectif_paiement) FROM stdin;
a0b901e9-f053-4623-b96d-1b444c777fd9	a0afad02-02c7-4eb0-abea-f0699eaf505d	a0ae0b56-e1a8-4753-987f-d12f5d29544d	5328125.00	3	2025-12-31 07:23:46	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 07:03:35	2025-12-31 07:24:04	\N	\N
a0af3d14-19c3-426d-b3d0-acd65ab68a3b	a0af3c84-842c-4de1-ba2b-6babad8c8d28	a0ae0b56-e1a8-4753-987f-d12f5d29544d	20000000.00	3	2025-12-26 10:31:01	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 10:30:44	2025-12-26 10:42:01	\N	\N
a0afadc7-bac5-4c4b-9e52-3309a1d8f233	a0afad02-02c7-4eb0-abea-f0699eaf505d	a0ae0b56-e1a8-4753-987f-d12f5d29544d	15000000.00	3	2025-12-26 15:46:04	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 15:45:52	2025-12-26 15:46:11	\N	\N
a0b909ad-878d-4572-85d5-4c6255fbed33	a0afad02-02c7-4eb0-abea-f0699eaf505d	a0ae0b56-e1a8-4753-987f-d12f5d29544d	5328125.00	3	2025-12-31 07:25:22	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 07:25:17	2025-12-31 07:25:26	\N	\N
a0b7d973-73d9-4c63-a15d-bc54235cb1e6	a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0ae0b56-e1a8-4753-987f-d12f5d29544d	7500000.00	3	2025-12-30 17:28:39	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 17:14:36	2025-12-30 17:28:57	\N	\N
a0b7deec-838e-49ce-9ddf-4bccdd2d6076	a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0ae0b56-e1a8-4753-987f-d12f5d29544d	3750000.00	3	2025-12-30 17:30:00	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 17:29:55	2025-12-30 17:30:04	\N	\N
a0b7df20-1e5e-4805-8564-110b82063ab3	a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0ae0b56-e1a8-4753-987f-d12f5d29544d	1875000.00	3	2025-12-30 17:30:33	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 17:30:28	2025-12-30 17:30:37	\N	\N
a0b7e083-28c8-430c-af4e-c86937080ed7	a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0ae0b56-e1a8-4753-987f-d12f5d29544d	937500.00	3	2025-12-30 17:34:29	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 17:34:21	2025-12-30 17:34:35	\N	\N
a0b7e0bb-1817-4312-9777-8149e69f1396	a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0ae0b56-e1a8-4753-987f-d12f5d29544d	468750.00	3	2025-12-30 17:35:03	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 17:34:58	2025-12-30 17:35:26	\N	\N
a0b7e165-f681-48da-800d-c17efb821689	a0b7d04d-2133-4ad8-bdca-b4f51ac4c4da	a0ae0b56-e1a8-4753-987f-d12f5d29544d	468750.00	3	2025-12-30 17:36:57	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 17:36:50	2025-12-30 17:37:05	\N	\N
a0b5305d-9927-45fa-a938-2be331ac432e	a0afad02-02c7-4eb0-abea-f0699eaf505d	a0ae0b56-e1a8-4753-987f-d12f5d29544d	50000.00	3	2025-12-30 17:56:20	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-29 09:30:11	2025-12-30 17:56:24	\N	\N
a0b8f480-cc68-49c0-b5eb-47d303f6e119	a0afad02-02c7-4eb0-abea-f0699eaf505d	a0ae0b56-e1a8-4753-987f-d12f5d29544d	63937500.00	3	2025-12-31 06:32:19	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 06:26:06	2025-12-31 06:32:25	\N	2025-12-31
a0b8ff29-a231-4725-b409-b7ab4fce1916	a0afad02-02c7-4eb0-abea-f0699eaf505d	a0ae0b56-e1a8-4753-987f-d12f5d29544d	10656250.00	3	2025-12-31 06:56:04	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 06:55:53	2025-12-31 07:03:13	\N	\N
\.


--
-- TOC entry 5406 (class 0 OID 48151)
-- Dependencies: 223
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 5409 (class 0 OID 48192)
-- Dependencies: 226
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.permissions (id, name, slug, description, resource, action, guard_name, category, priority, is_active, is_system, conditions, created_by, updated_by, last_used_at, created_at, updated_at, deleted_at) FROM stdin;
a0acfc74-ffd8-45aa-aab8-1732f786669e	Gérer les utilisateurs	users-manage	Permet de gérer toutes les actions sur les utilisateurs	users	manage	web	Gestion des utilisateurs	20	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-8e8e-435b-a098-e9ad5c87e1fb	Créer des utilisateurs	users-create	Permet de créer de nouveaux utilisateurs	users	create	web	Gestion des utilisateurs	10	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-90bd-4027-825e-f5639b32206e	Voir les utilisateurs	users-read	Permet de consulter la liste des utilisateurs	users	read	web	Gestion des utilisateurs	5	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-92c1-48dd-8fdc-36cf6ba8b6c0	Modifier les utilisateurs	users-update	Permet de modifier les informations des utilisateurs	users	update	web	Gestion des utilisateurs	10	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9473-42bc-8c86-f1ede4c77180	Supprimer les utilisateurs	users-delete	Permet de supprimer des utilisateurs	users	delete	web	Gestion des utilisateurs	15	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9609-43af-a743-63db217c3199	Exporter les utilisateurs	users-export	Permet d'exporter la liste des utilisateurs	users	export	web	Gestion des utilisateurs	5	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-97a1-46eb-a425-48ec83a0441f	Importer des utilisateurs	users-import	Permet d'importer des utilisateurs depuis un fichier	users	import	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9949-4e16-b3d5-22cd129ca0ba	Valider les utilisateurs	users-validate	Permet de valider les comptes utilisateurs	users	validate	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9af4-4747-bc97-668fa562f564	Rejecter les utilisateurs	users-reject	Permet de rejetter les comptes utilisateurs	users	reject	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9c46-4bcd-b202-d7dbb4cd3e53	Restaurer les utilisateurs	users-restore	Permet de restaurer les comptes utilisateurs supprimés	users	restore	web	Gestion des utilisateurs	15	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9d9e-4bdf-a534-5012b1982b92	Dupliquer les utilisateurs	users-duplicate	Permet de dupliquer les comptes utilisateurs	users	duplicate	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-9f36-43c3-9864-4fcc702f373a	Télécharger les utilisateurs	users-download	Permet de télécharger les informations des utilisateurs	users	download	web	Gestion des utilisateurs	5	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a0cb-408e-b1ea-cb906e90684c	Créer des rôles	roles-create	Permet de créer de nouveaux rôles	roles	create	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a24f-422a-8978-a007ff9da136	Voir les rôles	roles-read	Permet de consulter la liste des rôles	roles	read	web	Gestion des rôles	5	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a3da-4c2e-8534-dec81dd4c6ac	Modifier les rôles	roles-update	Permet de modifier les rôles	roles	update	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a552-4e59-acd8-60d43566656a	Supprimer les rôles	roles-delete	Permet de supprimer des rôles	roles	delete	web	Gestion des rôles	15	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a6f4-4900-9f5f-f75a9b1c6ed8	Assigner des rôles	roles-assign	Permet d'assigner des rôles aux utilisateurs	roles	update	web	Gestion des rôles	15	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a876-4562-b918-16b3522821a6	Exporter les rôles	roles-export	Permet d'exporter la liste des rôles	roles	export	web	Gestion des rôles	5	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-a94e-4a3c-8dea-6a6c2fd0f464	Importer des rôles	roles-import	Permet d'importer des rôles depuis un fichier	roles	import	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-aa4c-43eb-8fe4-f3200a8703bb	Dupliquer les rôles	roles-duplicate	Permet de dupliquer les rôles	roles	duplicate	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-abae-4fa0-9f33-eb7a5a4c021a	Télécharger les rôles	roles-download	Permet de télécharger les informations des rôles	roles	download	web	Gestion des rôles	5	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-ae1b-4beb-9c20-8307b5648697	Restaurer les rôles	roles-restore	Permet de restaurer les rôles supprimés	roles	restore	web	Gestion des rôles	15	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-afed-410e-b1ee-e0b14021bda1	Gérer les rôles	roles-manage	Permet de gérer toutes les actions sur les rôles	roles	manage	web	Gestion des rôles	20	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-b199-4c8f-9a7e-df70257c220b	Gérer les permissions	permissions-manage	Permet de gérer toutes les permissions	permissions	manage	web	Gestion des permissions	20	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-b311-4e7d-96b6-fda2aa6eee78	Voir les permissions	permissions-read	Permet de consulter les permissions	permissions	read	web	Gestion des permissions	5	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-b483-4fd1-9137-e4730fe26f2d	Assigner des permissions	permissions-assign	Permet d'assigner des permissions aux rôles	permissions	update	web	Gestion des permissions	15	t	t	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-b610-497a-8bf9-3381d4a0e5ba	Voir le dashboard	dashboard-read	Permet d'accéder au tableau de bord	dashboard	read	web	Dashboard	1	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-b782-4ad9-b69d-2aec6e34c54e	Voir les rapports	reports-read	Permet de consulter les rapports	reports	read	web	Rapports	5	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-b8fe-4a5e-9806-f30765ec651d	Exporter les rapports	reports-export	Permet d'exporter les rapports	reports	export	web	Rapports	5	t	f	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
\.


--
-- TOC entry 5408 (class 0 OID 48177)
-- Dependencies: 225
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5417 (class 0 OID 48534)
-- Dependencies: 235
-- Data for Name: prestataires; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prestataires (id_prestataire, raison_sociale_prestataire, numero_identification_prestataire, email_prestataire, numero_cc_prestataire, numero_rccm_prestataire, telephone_principal_prestataire, telephone_secondaire_prestataire, adresse_prestataire, ville_prestataire, pays_prestataire, representant_legal_prestataire, statut_prestataire, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0ae078c-1897-48de-8e7a-867ef2d066d8	SOCIETE GENERALE DE CONSTRUCTION NAVALE (SGCN SRL)	CI-ABJ-2025-001	sociatygcn@sosgcn.com	CI-123456780	RCCM-ABJ-2025-B-12345	+225014101203254	+225014101203854	Abidjan Plateau, Côte d'Ivoire, rue 225	Abidjan	Côte d'Ivoire	[{"nom":"KOUASSI Jean-Luc","contact":"+2250012458501","email":"jeanluc.kouassi@gmail.com","nationalite":"Ivoiriennes","pays":"C\\u00f4te d'Ivoire","adresse":"Abidjan Cocody, rue des golfes","profession":"Directeur G\\u00e9n\\u00e9rale","date_naissance":"1989-06-15","lieu_naissance":"Yamoussoukro","numero_piece_identite":"CI 201 21 01022","type_piece_identite":"CNI","date_delivrance":"2020-07-05","lieu_delivrance":"Yamoussoukro","date_expiration":"2030-07-04","id":"ea71a969-3885-469c-aeba-88351750b6d9","statut":1,"created_at":"2025-12-25T20:05:13+00:00"}]	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 20:05:15	2025-12-25 20:05:15	\N
a0af6c59-ef06-4bf7-97ef-fec58547831c	SOCIETE APHA GOF (SAG)	SA-12525845	societeapha@gof.com	87153814K	ZCCM-ABJ-2024-B-12345	+2250120023120	+2250123001000	Abidjan Côte d'Ivoire	Abidjan	Côte d'Ivoire	[{"nom":"KOUASSI Yao","contact":"+22501120102","email":"yao@gmail.com","nationalite":"Ivoirienne","pays":"C\\u00f4te d'Ivoire","adresse":"Abidjan C\\u00f4te d'Ivoire","profession":"Directeur G\\u00e9n\\u00e9ral","date_naissance":"1988-06-08","lieu_naissance":"Abidjan","numero_piece_identite":"CI 5255252222","type_piece_identite":"CNI","date_delivrance":"2025-12-09","lieu_delivrance":"Abidjan","date_expiration":"2026-11-07","id":"7cee2324-c762-4734-8e1b-515819c107d0","statut":1,"created_at":"2025-12-26T12:42:55+00:00"}]	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 12:42:55	2025-12-26 12:42:55	\N
\.


--
-- TOC entry 5426 (class 0 OID 48875)
-- Dependencies: 244
-- Data for Name: prestataires_lots; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prestataires_lots (id_attribution, prestataire_id, lot_id, proforma_id, version_attribution, is_active, numero_attribution, date_attribution, date_debut_prevue, date_fin_prevue, date_debut_reelle, date_fin_reelle, statut_attribution, motif_suspension, date_suspension, date_reprise_prevue, date_reprise_reelle, motif_retrait, date_retrait, type_retrait, jours_retard, taux_penalites, penalites_appliquees, penalites_payees, pourcentage_avancement, montant_engage, montant_paye, observations, conditions_particulieres, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_attribution_id) FROM stdin;
a0ae6916-365e-4cfa-98c7-437f0b1250e5	a0ae078c-1897-48de-8e7a-867ef2d066d8	a0ae0f46-357b-43e2-af5e-f2e422a91e41	a0ae6915-de64-4c5b-acd2-df2742e3bdcc	1	t	ATT-2025-0001	2025-12-26	\N	\N	\N	\N	1	\N	\N	\N	\N	\N	\N	\N	0	0.00	0.00	0.00	100.00	0.00	0.00	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 00:37:58	2025-12-26 03:26:02	\N	\N
a0af67ef-65a2-468b-a3d4-234546270174	a0ae078c-1897-48de-8e7a-867ef2d066d8	a0af507f-239c-4b4b-8598-34f54238aa16	a0af67ef-39bd-4b60-aab7-f7f55dabd04e	1	t	ATT-2025-0002	2025-12-26	\N	\N	\N	2025-12-26	1	\N	\N	\N	\N	\N	\N	\N	0	0.00	0.00	0.00	100.00	100300000.00	0.00	\n[Terminé] Observations	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 12:30:34	2025-12-26 15:26:38	\N	\N
a0b785bc-65ad-4c09-b5e4-e7dc85f8a93c	a0af6c59-ef06-4bf7-97ef-fec58547831c	a0b75169-b3a6-44b0-9765-e59fba89f945	a0b785bc-2847-4902-9fa0-f7aa03a41e73	1	t	ATT-2025-0003	2025-12-30	\N	\N	\N	\N	1	\N	\N	\N	\N	\N	\N	\N	0	0.00	0.00	0.00	0.00	0.00	0.00	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:20:31	2025-12-30 13:20:31	\N	\N
a0b794c1-d15a-4d33-af88-862e6ece9753	a0ae078c-1897-48de-8e7a-867ef2d066d8	a0b75b2a-ae5b-4e68-90a5-9a8902d80cac	a0b794c1-aa70-4771-bf98-1a57e0c854ff	1	t	ATT-2025-0005	2025-12-30	\N	\N	\N	\N	1	\N	\N	\N	\N	\N	\N	\N	0	0.00	0.00	0.00	0.00	0.00	0.00	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 14:02:31	2025-12-30 14:02:31	\N	\N
a0b78fd6-4ecd-4bfa-ba55-0966d0180d9c	a0af6c59-ef06-4bf7-97ef-fec58547831c	a0b75ab3-080b-41d0-9eab-ea2f12b25190	a0b78fd1-998b-437c-9ff6-75cdb3a45f46	1	t	ATT-2025-0004	2025-12-30	\N	\N	\N	\N	1	\N	\N	\N	\N	\N	\N	\N	0	0.00	0.00	0.00	50.00	0.00	0.00	\N	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-30 13:48:46	2025-12-30 16:17:55	\N	\N
\.


--
-- TOC entry 5414 (class 0 OID 48400)
-- Dependencies: 232
-- Data for Name: proformas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.proformas (id_proforma, version_proforma, numero_proforma, date_proforma, date_debut_validee_proforma, date_redemarrage_proforma, date_fin_validee_proforma, montant_retenu_proforma, taxe_montant, remise_montant_proforma, modalite_proforma, penalites_proforma, motif_modification_proforma, actif_proforma, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id) FROM stdin;
a0ae6915-de64-4c5b-acd2-df2742e3bdcc	1	PROF-2025-0001	2025-12-26	2025-12-27	2025-12-28	2025-12-29	165000000.00	29700000.00	2607000.00	30% avant livraison et 70% après livraison.	5.50	\N	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 00:37:58	2025-12-26 00:37:58	\N	\N
a0af67ef-39bd-4b60-aab7-f7f55dabd04e	1	PF2026	2025-12-26	2025-12-18	2025-12-18	2026-01-31	85000000.00	15300000.00	0.00	\N	0.00	\N	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-26 12:30:34	2025-12-26 12:30:34	\N	\N
a0b785bc-2847-4902-9fa0-f7aa03a41e73	1	PROF-2025-0002	2025-12-30	2025-12-31	2025-12-31	2026-01-02	25000000.00	0.00	0.00	30% à la commande et 70% à la livraison.	0.00	\N	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:20:31	2025-12-30 13:20:31	\N	\N
a0b78fd1-998b-437c-9ff6-75cdb3a45f46	1	PROF-2025-0003	2025-12-30	2025-12-31	2025-12-31	2026-01-02	35000000.00	0.00	0.00	40% à l'initialisation	0.00	\N	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 13:48:46	2025-12-30 13:48:46	\N	\N
a0b794c1-aa70-4771-bf98-1a57e0c854ff	1	PROF-2025-0004	2025-12-30	2025-12-31	2025-12-31	2026-01-01	15000000.00	0.00	0.00	25% au début et 75% à la livraison.	0.00	\N	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-30 14:02:31	2025-12-30 14:02:31	\N	\N
\.


--
-- TOC entry 5410 (class 0 OID 48232)
-- Dependencies: 227
-- Data for Name: role_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.role_permissions (role_id, permission_id, attribue_par, attribue_le, expire_le, actif, conditions, notes, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc74-ffd8-45aa-aab8-1732f786669e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-8e8e-435b-a098-e9ad5c87e1fb	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-90bd-4027-825e-f5639b32206e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-92c1-48dd-8fdc-36cf6ba8b6c0	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9473-42bc-8c86-f1ede4c77180	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9609-43af-a743-63db217c3199	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-97a1-46eb-a425-48ec83a0441f	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9949-4e16-b3d5-22cd129ca0ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9af4-4747-bc97-668fa562f564	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9c46-4bcd-b202-d7dbb4cd3e53	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9d9e-4bdf-a534-5012b1982b92	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-9f36-43c3-9864-4fcc702f373a	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a0cb-408e-b1ea-cb906e90684c	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a24f-422a-8978-a007ff9da136	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a3da-4c2e-8534-dec81dd4c6ac	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a552-4e59-acd8-60d43566656a	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a6f4-4900-9f5f-f75a9b1c6ed8	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a876-4562-b918-16b3522821a6	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-a94e-4a3c-8dea-6a6c2fd0f464	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-aa4c-43eb-8fe4-f3200a8703bb	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-abae-4fa0-9f33-eb7a5a4c021a	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-ae1b-4beb-9c20-8307b5648697	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-afed-410e-b1ee-e0b14021bda1	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-b199-4c8f-9a7e-df70257c220b	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-b311-4e7d-96b6-fda2aa6eee78	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-b483-4fd1-9137-e4730fe26f2d	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-b610-497a-8bf9-3381d4a0e5ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-b782-4ad9-b69d-2aec6e34c54e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	a0acfc77-b8fe-4a5e-9806-f30765ec651d	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc74-ffd8-45aa-aab8-1732f786669e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-8e8e-435b-a098-e9ad5c87e1fb	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-90bd-4027-825e-f5639b32206e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-92c1-48dd-8fdc-36cf6ba8b6c0	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9473-42bc-8c86-f1ede4c77180	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9609-43af-a743-63db217c3199	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-97a1-46eb-a425-48ec83a0441f	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9949-4e16-b3d5-22cd129ca0ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9af4-4747-bc97-668fa562f564	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9c46-4bcd-b202-d7dbb4cd3e53	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9d9e-4bdf-a534-5012b1982b92	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-9f36-43c3-9864-4fcc702f373a	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a0cb-408e-b1ea-cb906e90684c	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a24f-422a-8978-a007ff9da136	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a3da-4c2e-8534-dec81dd4c6ac	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a552-4e59-acd8-60d43566656a	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a6f4-4900-9f5f-f75a9b1c6ed8	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a876-4562-b918-16b3522821a6	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-a94e-4a3c-8dea-6a6c2fd0f464	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-aa4c-43eb-8fe4-f3200a8703bb	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-abae-4fa0-9f33-eb7a5a4c021a	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-ae1b-4beb-9c20-8307b5648697	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-afed-410e-b1ee-e0b14021bda1	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-b610-497a-8bf9-3381d4a0e5ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-b782-4ad9-b69d-2aec6e34c54e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	a0acfc77-b8fe-4a5e-9806-f30765ec651d	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	a0acfc77-8e8e-435b-a098-e9ad5c87e1fb	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	a0acfc77-90bd-4027-825e-f5639b32206e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	a0acfc77-92c1-48dd-8fdc-36cf6ba8b6c0	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	a0acfc77-a24f-422a-8978-a007ff9da136	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	a0acfc77-b610-497a-8bf9-3381d4a0e5ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	a0acfc77-b782-4ad9-b69d-2aec6e34c54e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c1ba-4040-9975-19d6024df0a3	a0acfc77-90bd-4027-825e-f5639b32206e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c1ba-4040-9975-19d6024df0a3	a0acfc77-b610-497a-8bf9-3381d4a0e5ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c1ba-4040-9975-19d6024df0a3	a0acfc77-b782-4ad9-b69d-2aec6e34c54e	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c32c-4526-b84e-b09dcf38b0fa	a0acfc77-b610-497a-8bf9-3381d4a0e5ba	\N	2025-12-25 07:38:25	\N	t	\N	\N	\N	\N	\N	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
\.


--
-- TOC entry 5404 (class 0 OID 48095)
-- Dependencies: 221
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, name, slug, description, level, is_system_role, created_at, updated_at, deleted_at) FROM stdin;
a0acfc77-bb3b-43a5-9449-7f81b9daf3be	Super Administrateur	super-admin	Accès complet à toutes les fonctionnalités du système	100	t	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-be70-422b-b88e-a159ff77c61b	Administrateur	admin	Gestion complète du système avec quelques restrictions	80	t	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c035-4e8a-be45-6c5821aac0fa	Manager	manager	Gestion des utilisateurs et des contenus	60	f	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c1ba-4040-9975-19d6024df0a3	Éditeur	editor	Modification et gestion des contenus	40	f	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
a0acfc77-c32c-4526-b84e-b09dcf38b0fa	Utilisateur	user	Accès de base au système	20	f	2025-12-25 07:38:25	2025-12-25 07:38:25	\N
\.


--
-- TOC entry 5422 (class 0 OID 48733)
-- Dependencies: 240
-- Data for Name: situations_financieres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.situations_financieres (id_situation_financiere, prestataire_id, exercice_fiscal_situation_financiere, chiffre_affaire_situation_financiere, fonds_propres_situation_financiere, capacite_emprunt_situation_financiere, ratio_solvabilite_situation_financiere, ratio_liquidite_situation_financiere, resultat_net_situation_financiere, total_actif_situation_financiere, total_passif_situation_financiere, observations_situation_financiere, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0b9ab16-2037-4eab-8ecb-ce284c05dbde	a0af6c59-ef06-4bf7-97ef-fec58547831c	2025	15000000000.00	25000000000.00	17000000000.00	15.00	0.50	14000000000.00	17000000000.00	14000000000.00	\N	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-31 14:56:37	2025-12-31 14:57:22	\N
\.


--
-- TOC entry 5411 (class 0 OID 48287)
-- Dependencies: 229
-- Data for Name: types_appels_offres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.types_appels_offres (id_type_appel_offre, libelle_type_appel_offre, code_type_appel_offre, valeur_minimuim_type_appel_offre, valeur_maximuim_type_appel_offre, description_critere_type_appel_offre, actif_type_appel_offre, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id, version_type_appel_offre, motif_modification_type_appel_offre) FROM stdin;
a0ad5ed9-6b40-4295-b11f-bb294113e2af	APPEL D'OFFRE RESTREINT	AOR	20000001.00	50000000.00	Seules les entreprises pré-sélectionnées ou invitées peuvent soumissionner.\r\n\r\nCaractéristiques :\r\n\r\nNombre limité de candidats\r\n\r\nSélection basée sur des critères techniques et financiers\r\nAvantages :\r\nGain de temps\r\nOffres plus ciblées et qualitatives	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 12:13:30	2025-12-25 12:13:30	\N	\N	1	\N
a0ad6950-63ec-4588-a41c-14244604c6ec	APPEL D'OFFRES A DEUX ENVELOPPES	AODE	150000001.00	200000000.00	Les soumissionnaires déposent :\r\nune offre technique\r\nune offre financière, analysées séparément	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	\N	2025-12-25 12:42:46	2025-12-25 12:42:46	\N	\N	1	\N
a0ad60cb-b4c2-44f6-89bd-3818680c67fa	APPEL D'OFFRE AVEC PREQUALIFICATION	AOAP	15000001.00	250000000.00	Étape préalable visant à vérifier la capacité technique, financière et administrative des entreprises avant l’appel d’offres final.	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 12:18:57	2025-12-29 11:24:06	\N	\N	1	\N
a0ad5ffe-56d8-4593-adbc-dcbf4e4b25d9	APPEL D'OFFRE INTERNATIONAL	AOI	90000001.00	150000000.00	Ouvert aux entreprises étrangères et nationales.	f	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 12:16:42	2025-12-29 11:56:55	\N	\N	1	\N
a0af47a5-0c9b-4715-8616-40e2e7e15fe1	Appel d’offres sur concours	AOC	200000001.00	250000000.00	L’appel d’offres sur concours est une procédure de passation de marché par laquelle le maître d’ouvrage invite des candidats à proposer des solutions techniques, architecturales ou conceptuelles répondant à un besoin spécifique. Les offres sont évaluées principalement sur la qualité technique, la créativité, l’innovation et la pertinence des solutions proposées, et non uniquement sur le prix.\r\n\r\nCette procédure est généralement utilisée pour des projets nécessitant un haut niveau d’expertise, tels que les études architecturales, l’ingénierie, l’urbanisme ou le design. Les propositions sont examinées par un jury ou une commission spécialisée, qui sélectionne la meilleure solution. Le lauréat du concours peut ensuite se voir attribuer le marché correspondant, conformément aux règles en vigueur...	t	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-26 11:00:17	2025-12-26 11:01:31	\N	\N	1	\N
a0ad5f61-0932-4ae3-9e7e-820c18024099	APPEL D'OFFRE NATIONAL	AON	50000001.00	90000000.00	Réservé aux entreprises du pays concerné.\r\nCaractéristiques :\r\nFavorise les entreprises locales\r\nProcédures adaptées au contexte national\r\nAvantages :\r\nDéveloppement économique local\r\nRéduction des coûts logistiques	f	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 12:14:59	2025-12-29 11:57:05	\N	\N	1	\N
a0ad5e41-dfe1-4695-94c8-f15afad2678d	APPEL D'OFFRE OUVERT	AOO	1.00	20000000.00	Procédure dans laquelle toute entreprise qualifiée peut soumettre une offre, sans restriction préalable.\r\nCaractéristiques :\r\nLarge concurrence\r\nPublication publique (journaux, plateformes officielles)\r\nTransparence élevée\r\nAvantages :\r\nMeilleur rapport qualité/prix\r\nÉgalité de traitement	f	c10a2c28-bcbd-477a-aa10-73ddee5200ac	c10a2c28-bcbd-477a-aa10-73ddee5200ac	\N	2025-12-25 12:11:51	2025-12-29 11:58:28	\N	\N	1	\N
\.


--
-- TOC entry 5405 (class 0 OID 48112)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, nom_complet, email, password, telephone_principal, telephone_secondaire, role_id, email_verified_at, statut, created_at, updated_at, deleted_at, created_by, updated_by, deleted_by) FROM stdin;
cbce60ed-83a3-4a39-8331-e3a4d9660d25	Allangba Koné	direction@isittci.com	$2y$12$Mol8.5JirRg2x5JWu2cQ7.Q2jEBFG8HyI95Hxzh8Es2I8JNaZK39m	+2250000000010	+2250000000011	a0acfc77-bb3b-43a5-9449-7f81b9daf3be	2025-12-25 07:38:26	1	2025-12-25 07:38:26	2025-12-25 07:38:26	\N	\N	\N	\N
c10a2c28-bcbd-477a-aa10-73ddee5200ac	DJOBO NDRI	nfcdjobo@gmail.com	$2y$12$zU4v.W9XvdUWE7fBZ8zr6.XgQGFBN/C9.ysKgma2vCW.asUauVdmO	+2250200000000	+225010100000	a0acfc77-bb3b-43a5-9449-7f81b9daf3be	2025-12-31 06:15:40	1	2025-12-25 07:38:26	2025-12-31 06:15:40	\N	\N	\N	\N
\.


--
-- TOC entry 5627 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 27, true);


--
-- TOC entry 5158 (class 2606 OID 49037)
-- Name: alertes alertes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_pkey PRIMARY KEY (id);


--
-- TOC entry 5102 (class 2606 OID 48358)
-- Name: appels_offres appels_offres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_pkey PRIMARY KEY (id_appel_offre);


--
-- TOC entry 5126 (class 2606 OID 48699)
-- Name: banques banques_code_banque_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_code_banque_unique UNIQUE (code_banque);


--
-- TOC entry 5128 (class 2606 OID 48701)
-- Name: banques banques_numero_compte_banque_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_numero_compte_banque_unique UNIQUE (numero_compte_banque);


--
-- TOC entry 5130 (class 2606 OID 48697)
-- Name: banques banques_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_pkey PRIMARY KEY (id_banque);


--
-- TOC entry 5132 (class 2606 OID 48732)
-- Name: capacites_techniques capacites_techniques_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_pkey PRIMARY KEY (id_capacite_technique);


--
-- TOC entry 5104 (class 2606 OID 48394)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_pkey PRIMARY KEY (id_caracteristique_appel_offre);


--
-- TOC entry 5112 (class 2606 OID 48533)
-- Name: criteres_evaluations criteres_evaluations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_pkey PRIMARY KEY (id_critere_evaluation);


--
-- TOC entry 5124 (class 2606 OID 48665)
-- Name: documents documents_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_pkey PRIMARY KEY (id_document);


--
-- TOC entry 5151 (class 2606 OID 49013)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_pkey PRIMARY KEY (id_evaluation_critere);


--
-- TOC entry 5116 (class 2606 OID 48628)
-- Name: evaluations evaluations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_pkey PRIMARY KEY (id_evaluation);


--
-- TOC entry 5136 (class 2606 OID 48796)
-- Name: evaluations_prestataires evaluations_prestataires_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_pkey PRIMARY KEY (id_evaluation_prestataire);


--
-- TOC entry 5138 (class 2606 OID 48836)
-- Name: factures factures_numero_facture_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factures
    ADD CONSTRAINT factures_numero_facture_unique UNIQUE (numero_facture);


--
-- TOC entry 5140 (class 2606 OID 48834)
-- Name: factures factures_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factures
    ADD CONSTRAINT factures_pkey PRIMARY KEY (id_facture);


--
-- TOC entry 5072 (class 2606 OID 48174)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5074 (class 2606 OID 48176)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 5156 (class 2606 OID 49008)
-- Name: evaluations_lots_prestataires idx_unique_eval_critere_prestataire; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT idx_unique_eval_critere_prestataire UNIQUE (critere_evaluation_id, evaluation_id, prestataire_id);


--
-- TOC entry 5110 (class 2606 OID 48489)
-- Name: lots lots_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_pkey PRIMARY KEY (id_lot);


--
-- TOC entry 5058 (class 2606 OID 48094)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5142 (class 2606 OID 48874)
-- Name: paiements paiements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_pkey PRIMARY KEY (id_paiement);


--
-- TOC entry 5070 (class 2606 OID 48159)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 5087 (class 2606 OID 48229)
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 5089 (class 2606 OID 48231)
-- Name: permissions permissions_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_slug_unique UNIQUE (slug);


--
-- TOC entry 5076 (class 2606 OID 48189)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 5078 (class 2606 OID 48191)
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- TOC entry 5149 (class 2606 OID 48943)
-- Name: prestataires_lots prestataires_lots_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_pkey PRIMARY KEY (id_attribution);


--
-- TOC entry 5114 (class 2606 OID 48571)
-- Name: prestataires prestataires_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_pkey PRIMARY KEY (id_prestataire);


--
-- TOC entry 5106 (class 2606 OID 48443)
-- Name: proformas proformas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_pkey PRIMARY KEY (id_proforma);


--
-- TOC entry 5098 (class 2606 OID 48261)
-- Name: role_permissions role_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_pkey PRIMARY KEY (role_id, permission_id);


--
-- TOC entry 5060 (class 2606 OID 48109)
-- Name: roles roles_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_unique UNIQUE (name);


--
-- TOC entry 5062 (class 2606 OID 48107)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 5064 (class 2606 OID 48111)
-- Name: roles roles_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_slug_unique UNIQUE (slug);


--
-- TOC entry 5134 (class 2606 OID 48764)
-- Name: situations_financieres situations_financieres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_pkey PRIMARY KEY (id_situation_financiere);


--
-- TOC entry 5100 (class 2606 OID 48318)
-- Name: types_appels_offres types_appels_offres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_pkey PRIMARY KEY (id_type_appel_offre);


--
-- TOC entry 5091 (class 2606 OID 48217)
-- Name: permissions unique_permission_per_guard; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT unique_permission_per_guard UNIQUE (slug, guard_name);


--
-- TOC entry 5066 (class 2606 OID 48135)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 5068 (class 2606 OID 48133)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 5152 (class 1259 OID 49011)
-- Name: idx_elp_critere; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_elp_critere ON public.evaluations_lots_prestataires USING btree (critere_evaluation_id);


--
-- TOC entry 5153 (class 1259 OID 49009)
-- Name: idx_elp_evaluation; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_elp_evaluation ON public.evaluations_lots_prestataires USING btree (evaluation_id);


--
-- TOC entry 5154 (class 1259 OID 49010)
-- Name: idx_elp_prestataire; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_elp_prestataire ON public.evaluations_lots_prestataires USING btree (prestataire_id);


--
-- TOC entry 5117 (class 1259 OID 48955)
-- Name: idx_evaluation_attribution; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_attribution ON public.evaluations USING btree (attribution_id);


--
-- TOC entry 5118 (class 1259 OID 48626)
-- Name: idx_evaluation_is_current; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_is_current ON public.evaluations USING btree (is_current);


--
-- TOC entry 5119 (class 1259 OID 48625)
-- Name: idx_evaluation_numero_current; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_numero_current ON public.evaluations USING btree (numero_evaluation, is_current);


--
-- TOC entry 5120 (class 1259 OID 48634)
-- Name: idx_evaluation_parent; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_parent ON public.evaluations USING btree (evaluation_parent_id);


--
-- TOC entry 5121 (class 1259 OID 48624)
-- Name: idx_evaluation_rang; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_rang ON public.evaluations USING btree (rang);


--
-- TOC entry 5122 (class 1259 OID 48623)
-- Name: idx_evaluation_statut; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_statut ON public.evaluations USING btree (statut_evaluation);


--
-- TOC entry 5143 (class 1259 OID 48937)
-- Name: idx_lot_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_lot_active ON public.prestataires_lots USING btree (lot_id, is_active);


--
-- TOC entry 5144 (class 1259 OID 48938)
-- Name: idx_lot_statut; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_lot_statut ON public.prestataires_lots USING btree (lot_id, statut_attribution);


--
-- TOC entry 5145 (class 1259 OID 48941)
-- Name: idx_numero_attribution; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_numero_attribution ON public.prestataires_lots USING btree (numero_attribution);


--
-- TOC entry 5080 (class 1259 OID 48212)
-- Name: idx_permissions_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_category ON public.permissions USING btree (category);


--
-- TOC entry 5081 (class 1259 OID 48215)
-- Name: idx_permissions_complete; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_complete ON public.permissions USING btree (resource, action, guard_name, is_active);


--
-- TOC entry 5082 (class 1259 OID 48211)
-- Name: idx_permissions_guard_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_guard_active ON public.permissions USING btree (guard_name, is_active);


--
-- TOC entry 5083 (class 1259 OID 48210)
-- Name: idx_permissions_resource_action; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_resource_action ON public.permissions USING btree (resource, action);


--
-- TOC entry 5084 (class 1259 OID 48213)
-- Name: idx_permissions_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_slug ON public.permissions USING btree (slug);


--
-- TOC entry 5085 (class 1259 OID 48214)
-- Name: idx_permissions_system_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_system_active ON public.permissions USING btree (is_system, is_active);


--
-- TOC entry 5146 (class 1259 OID 48940)
-- Name: idx_prestataire_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_prestataire_active ON public.prestataires_lots USING btree (prestataire_id, is_active);


--
-- TOC entry 5147 (class 1259 OID 48939)
-- Name: idx_prestataire_statut; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_prestataire_statut ON public.prestataires_lots USING btree (prestataire_id, statut_attribution);


--
-- TOC entry 5092 (class 1259 OID 48281)
-- Name: idx_role_permissions_actif_deleted; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_actif_deleted ON public.role_permissions USING btree (actif, deleted_at);


--
-- TOC entry 5093 (class 1259 OID 48279)
-- Name: idx_role_permissions_attribue_par; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_attribue_par ON public.role_permissions USING btree (attribue_par);


--
-- TOC entry 5094 (class 1259 OID 48280)
-- Name: idx_role_permissions_expiration; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_expiration ON public.role_permissions USING btree (expire_le);


--
-- TOC entry 5095 (class 1259 OID 48278)
-- Name: idx_role_permissions_perm_actif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_perm_actif ON public.role_permissions USING btree (permission_id, actif);


--
-- TOC entry 5096 (class 1259 OID 48277)
-- Name: idx_role_permissions_role_actif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_role_actif ON public.role_permissions USING btree (role_id, actif);


--
-- TOC entry 5107 (class 1259 OID 48486)
-- Name: lots_id_lot_numero_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX lots_id_lot_numero_index ON public.lots USING btree (id_lot, numero);


--
-- TOC entry 5108 (class 1259 OID 48487)
-- Name: lots_id_lot_version_lot_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX lots_id_lot_version_lot_index ON public.lots USING btree (id_lot, version_lot);


--
-- TOC entry 5079 (class 1259 OID 48187)
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- TOC entry 5251 (class 2606 OID 49021)
-- Name: alertes alertes_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5252 (class 2606 OID 49031)
-- Name: alertes alertes_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5253 (class 2606 OID 49026)
-- Name: alertes alertes_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5175 (class 2606 OID 48342)
-- Name: appels_offres appels_offres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5176 (class 2606 OID 48352)
-- Name: appels_offres appels_offres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5177 (class 2606 OID 48337)
-- Name: appels_offres appels_offres_type_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_type_appel_offre_id_foreign FOREIGN KEY (type_appel_offre_id) REFERENCES public.types_appels_offres(id_type_appel_offre) ON DELETE RESTRICT;


--
-- TOC entry 5178 (class 2606 OID 48347)
-- Name: appels_offres appels_offres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5213 (class 2606 OID 48681)
-- Name: banques banques_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5214 (class 2606 OID 48691)
-- Name: banques banques_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5215 (class 2606 OID 48676)
-- Name: banques banques_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE CASCADE;


--
-- TOC entry 5216 (class 2606 OID 48686)
-- Name: banques banques_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5217 (class 2606 OID 48716)
-- Name: capacites_techniques capacites_techniques_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5218 (class 2606 OID 48726)
-- Name: capacites_techniques capacites_techniques_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5219 (class 2606 OID 48711)
-- Name: capacites_techniques capacites_techniques_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5220 (class 2606 OID 48721)
-- Name: capacites_techniques capacites_techniques_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5179 (class 2606 OID 48373)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_appel_offre_id_foreign FOREIGN KEY (appel_offre_id) REFERENCES public.appels_offres(id_appel_offre) ON DELETE CASCADE;


--
-- TOC entry 5180 (class 2606 OID 48378)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5181 (class 2606 OID 48388)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5182 (class 2606 OID 48395)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.caracteristiques_appels_offres(id_caracteristique_appel_offre) ON DELETE SET NULL;


--
-- TOC entry 5183 (class 2606 OID 48383)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5193 (class 2606 OID 48517)
-- Name: criteres_evaluations criteres_evaluations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5194 (class 2606 OID 48527)
-- Name: criteres_evaluations criteres_evaluations_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5195 (class 2606 OID 48512)
-- Name: criteres_evaluations criteres_evaluations_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE CASCADE;


--
-- TOC entry 5196 (class 2606 OID 48522)
-- Name: criteres_evaluations criteres_evaluations_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5209 (class 2606 OID 48649)
-- Name: documents documents_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5210 (class 2606 OID 48659)
-- Name: documents documents_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5211 (class 2606 OID 48644)
-- Name: documents documents_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE SET NULL;


--
-- TOC entry 5212 (class 2606 OID 48654)
-- Name: documents documents_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5200 (class 2606 OID 48950)
-- Name: evaluations evaluations_attribution_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_attribution_id_foreign FOREIGN KEY (attribution_id) REFERENCES public.prestataires_lots(id_attribution) ON DELETE CASCADE;


--
-- TOC entry 5201 (class 2606 OID 48608)
-- Name: evaluations evaluations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5202 (class 2606 OID 49045)
-- Name: evaluations evaluations_critere_evaluation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_critere_evaluation_id_foreign FOREIGN KEY (critere_evaluation_id) REFERENCES public.criteres_evaluations(id_critere_evaluation) ON DELETE SET NULL;


--
-- TOC entry 5203 (class 2606 OID 48618)
-- Name: evaluations evaluations_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5204 (class 2606 OID 48593)
-- Name: evaluations evaluations_evaluateur_principal_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_evaluateur_principal_id_foreign FOREIGN KEY (evaluateur_principal_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5205 (class 2606 OID 48629)
-- Name: evaluations evaluations_evaluation_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_evaluation_parent_id_foreign FOREIGN KEY (evaluation_parent_id) REFERENCES public.evaluations(id_evaluation) ON DELETE SET NULL;


--
-- TOC entry 5245 (class 2606 OID 48992)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5246 (class 2606 OID 48977)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_critere_evaluation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_critere_evaluation_id_foreign FOREIGN KEY (critere_evaluation_id) REFERENCES public.criteres_evaluations(id_critere_evaluation) ON DELETE CASCADE;


--
-- TOC entry 5247 (class 2606 OID 49002)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5248 (class 2606 OID 48982)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_evaluation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_evaluation_id_foreign FOREIGN KEY (evaluation_id) REFERENCES public.evaluations(id_evaluation) ON DELETE CASCADE;


--
-- TOC entry 5249 (class 2606 OID 48987)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE CASCADE;


--
-- TOC entry 5250 (class 2606 OID 48997)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5225 (class 2606 OID 48780)
-- Name: evaluations_prestataires evaluations_prestataires_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5226 (class 2606 OID 48790)
-- Name: evaluations_prestataires evaluations_prestataires_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5227 (class 2606 OID 48775)
-- Name: evaluations_prestataires evaluations_prestataires_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5228 (class 2606 OID 48785)
-- Name: evaluations_prestataires evaluations_prestataires_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5206 (class 2606 OID 48603)
-- Name: evaluations evaluations_rejete_par_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_rejete_par_foreign FOREIGN KEY (rejete_par) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5207 (class 2606 OID 48613)
-- Name: evaluations evaluations_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5208 (class 2606 OID 48598)
-- Name: evaluations evaluations_valide_par_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_valide_par_foreign FOREIGN KEY (valide_par) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5229 (class 2606 OID 48818)
-- Name: factures factures_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factures
    ADD CONSTRAINT factures_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5230 (class 2606 OID 48828)
-- Name: factures factures_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factures
    ADD CONSTRAINT factures_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5231 (class 2606 OID 48813)
-- Name: factures factures_proforma_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factures
    ADD CONSTRAINT factures_proforma_id_foreign FOREIGN KEY (proforma_id) REFERENCES public.proformas(id_proforma) ON DELETE SET NULL;


--
-- TOC entry 5232 (class 2606 OID 48823)
-- Name: factures factures_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.factures
    ADD CONSTRAINT factures_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5188 (class 2606 OID 48466)
-- Name: lots lots_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_appel_offre_id_foreign FOREIGN KEY (appel_offre_id) REFERENCES public.appels_offres(id_appel_offre) ON DELETE CASCADE;


--
-- TOC entry 5189 (class 2606 OID 48471)
-- Name: lots lots_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5190 (class 2606 OID 48481)
-- Name: lots lots_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5191 (class 2606 OID 48490)
-- Name: lots lots_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.lots(id_lot) ON DELETE SET NULL;


--
-- TOC entry 5192 (class 2606 OID 48476)
-- Name: lots lots_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5233 (class 2606 OID 48853)
-- Name: paiements paiements_banque_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_banque_id_foreign FOREIGN KEY (banque_id) REFERENCES public.banques(id_banque) ON DELETE SET NULL;


--
-- TOC entry 5234 (class 2606 OID 48858)
-- Name: paiements paiements_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5235 (class 2606 OID 48868)
-- Name: paiements paiements_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5236 (class 2606 OID 48848)
-- Name: paiements paiements_facture_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_facture_id_foreign FOREIGN KEY (facture_id) REFERENCES public.factures(id_facture) ON DELETE SET NULL;


--
-- TOC entry 5237 (class 2606 OID 48863)
-- Name: paiements paiements_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5163 (class 2606 OID 48218)
-- Name: permissions permissions_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5164 (class 2606 OID 48223)
-- Name: permissions permissions_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5197 (class 2606 OID 48555)
-- Name: prestataires prestataires_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5198 (class 2606 OID 48565)
-- Name: prestataires prestataires_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5238 (class 2606 OID 48922)
-- Name: prestataires_lots prestataires_lots_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5239 (class 2606 OID 48932)
-- Name: prestataires_lots prestataires_lots_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5240 (class 2606 OID 48912)
-- Name: prestataires_lots prestataires_lots_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE CASCADE;


--
-- TOC entry 5241 (class 2606 OID 48944)
-- Name: prestataires_lots prestataires_lots_parent_attribution_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_parent_attribution_id_foreign FOREIGN KEY (parent_attribution_id) REFERENCES public.prestataires_lots(id_attribution) ON DELETE SET NULL;


--
-- TOC entry 5242 (class 2606 OID 48907)
-- Name: prestataires_lots prestataires_lots_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE CASCADE;


--
-- TOC entry 5243 (class 2606 OID 48917)
-- Name: prestataires_lots prestataires_lots_proforma_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_proforma_id_foreign FOREIGN KEY (proforma_id) REFERENCES public.proformas(id_proforma) ON DELETE CASCADE;


--
-- TOC entry 5244 (class 2606 OID 48927)
-- Name: prestataires_lots prestataires_lots_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5199 (class 2606 OID 48560)
-- Name: prestataires prestataires_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5184 (class 2606 OID 48427)
-- Name: proformas proformas_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5185 (class 2606 OID 48437)
-- Name: proformas proformas_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5186 (class 2606 OID 48444)
-- Name: proformas proformas_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.proformas(id_proforma) ON DELETE SET NULL;


--
-- TOC entry 5187 (class 2606 OID 48432)
-- Name: proformas proformas_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5165 (class 2606 OID 48272)
-- Name: role_permissions role_permissions_attribue_par_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_attribue_par_foreign FOREIGN KEY (attribue_par) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5166 (class 2606 OID 48245)
-- Name: role_permissions role_permissions_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5167 (class 2606 OID 48255)
-- Name: role_permissions role_permissions_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5168 (class 2606 OID 48267)
-- Name: role_permissions role_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 5169 (class 2606 OID 48262)
-- Name: role_permissions role_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 5170 (class 2606 OID 48250)
-- Name: role_permissions role_permissions_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5221 (class 2606 OID 48748)
-- Name: situations_financieres situations_financieres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5222 (class 2606 OID 48758)
-- Name: situations_financieres situations_financieres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5223 (class 2606 OID 48743)
-- Name: situations_financieres situations_financieres_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5224 (class 2606 OID 48753)
-- Name: situations_financieres situations_financieres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5171 (class 2606 OID 48302)
-- Name: types_appels_offres types_appels_offres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5172 (class 2606 OID 48312)
-- Name: types_appels_offres types_appels_offres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5173 (class 2606 OID 49040)
-- Name: types_appels_offres types_appels_offres_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.types_appels_offres(id_type_appel_offre) ON DELETE SET NULL;


--
-- TOC entry 5174 (class 2606 OID 48307)
-- Name: types_appels_offres types_appels_offres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5159 (class 2606 OID 48136)
-- Name: users users_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5160 (class 2606 OID 48146)
-- Name: users users_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5161 (class 2606 OID 48127)
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- TOC entry 5162 (class 2606 OID 48141)
-- Name: users users_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


-- Completed on 2025-12-31 15:11:49

--
-- PostgreSQL database dump complete
--

\unrestrict LVznHaWGJiKFJbd6xZmxZ6505MdpyezjlY2aLM2SJU1xeXVq0bYXtXbrnCdFl8N

