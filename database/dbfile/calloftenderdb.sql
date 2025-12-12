--
-- PostgreSQL database dump
--

\restrict S1CrU6kjx54zmVkQIclRsflFtcrrokuFFtioOmmSS71PGgb99K4qNQbKwfV0MI7

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2025-12-10 16:20:52

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
-- TOC entry 245 (class 1259 OID 40269)
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
-- TOC entry 230 (class 1259 OID 38265)
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
-- TOC entry 5384 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.type_appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.type_appel_offre_id IS 'Identifiant unique du type.';


--
-- TOC entry 5385 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.numero_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.numero_appel_offre IS 'Numéro officiel (ex: AOT-2025-045). Référence dans tous les documents.';


--
-- TOC entry 5386 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.libelle_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.libelle_critere_appel_offre IS 'Nom du lot (ex: Gros œuvre, Électricité, Plomberie).';


--
-- TOC entry 5387 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.objet_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.objet_critere_appel_offre IS 'Description officielle de ce qui est demandé.';


--
-- TOC entry 5388 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.montant_global_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.montant_global_appel_offre IS 'Montant total estimé pour cet appel d''offres.';


--
-- TOC entry 5389 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.description_critere_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.description_critere_critere_appel_offre IS 'Détail des travaux de ce critere';


--
-- TOC entry 5390 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.date_publication_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.date_publication_critere_appel_offre IS 'Date à laquelle l''appel d''offres a été publié.';


--
-- TOC entry 5391 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.date_limite_depot_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.date_limite_depot_critere_appel_offre IS 'Date limite pour le dépôt des offres.';


--
-- TOC entry 5392 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.date_ouverture_plis_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.date_ouverture_plis_critere_appel_offre IS 'Date prévue pour l''ouverture des plis.';


--
-- TOC entry 5393 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.statut_evaluation_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.statut_evaluation_critere_appel_offre IS 'Statut actuel de l''évaluation des offres. Pour savoir si actif ou non';


--
-- TOC entry 5394 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.conditions_participation_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.conditions_participation_critere_appel_offre IS 'Conditions requises pour participer à cet appel d''offres.';


--
-- TOC entry 5395 (class 0 OID 0)
-- Dependencies: 230
-- Name: COLUMN appels_offres.criteres_selection_critere_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.appels_offres.criteres_selection_critere_appel_offre IS 'Critères utilisés pour évaluer les offres reçues.';


--
-- TOC entry 238 (class 1259 OID 39960)
-- Name: banques; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.banques (
    id_banque uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    nom_banque character varying(150),
    code_banque character varying(25),
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
-- TOC entry 5396 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.id_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.id_banque IS 'Identifiant unique de la banque.';


--
-- TOC entry 5397 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.prestataire_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.prestataire_id IS 'Identifiant du prestataire associé.';


--
-- TOC entry 5398 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.nom_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.nom_banque IS 'Nom de la banque';


--
-- TOC entry 5399 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.code_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.code_banque IS 'Code banque';


--
-- TOC entry 5400 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.numero_compte_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.numero_compte_banque IS 'Numéro de compte bancaire';


--
-- TOC entry 5401 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.code_guichet_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.code_guichet_banque IS 'Code guichet bancaire';


--
-- TOC entry 5402 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.cle_rib_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.cle_rib_banque IS 'Clé RIB (Relevé d''Identité Bancaire)';


--
-- TOC entry 5403 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.iban_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.iban_banque IS 'International Bank Account Number';


--
-- TOC entry 5404 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.swift_bic_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.swift_bic_banque IS 'SWIFT/BIC code';


--
-- TOC entry 5405 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.titulaire_compte_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.titulaire_compte_banque IS 'Nom du titulaire du compte bancaire';


--
-- TOC entry 5406 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.actif_banque; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.actif_banque IS 'Permet de désactiver temporairement une banque sans la supprimer.';


--
-- TOC entry 5407 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.created_by IS 'Identifiant de l''utilisateur ayant créé la banque.';


--
-- TOC entry 5408 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.updated_by IS 'Identifiant de l''utilisateur ayant mis à jour la banque.';


--
-- TOC entry 5409 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.deleted_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.deleted_by IS 'Identifiant de l''utilisateur ayant supprimé la banque.';


--
-- TOC entry 5410 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.created_at IS 'Date de création de la banque.';


--
-- TOC entry 5411 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.updated_at IS 'Date de la dernière mise à jour de la banque.';


--
-- TOC entry 5412 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN banques.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.banques.deleted_at IS 'Date de suppression de la banque.';


--
-- TOC entry 239 (class 1259 OID 39991)
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
-- TOC entry 231 (class 1259 OID 38305)
-- Name: caracteristiques_appels_offres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.caracteristiques_appels_offres (
    id_caracteristique_appel_offre uuid CONSTRAINT caracteristiques_appels_off_id_caracteristique_appel_o_not_null NOT NULL,
    appel_offre_id uuid NOT NULL,
    version_caracteristique_appel_offre integer DEFAULT 1 CONSTRAINT caracteristiques_appels_off_version_caracteristique_ap_not_null NOT NULL,
    date_demarrage_prevue_caracteristique_appel_offre date,
    date_livraison_previsionnelle_caracteristique_appel_offre date,
    lieu_execution_caracteristique_appel_offre character varying(255),
    penalites_retard_journalier_caracteristique_appel_offre numeric(15,2),
    montant_garantie_caracteristique_appel_offre numeric(15,2),
    delai_garantie_jours_caracteristique_appel_offre numeric(15,2),
    conditions_paiement_caracteristique_appel_offre text,
    modalites_execution_caracteristique_appel_offre text,
    documents_requis_caracteristique_appel_offre text,
    autres_informations_caracteristique_appel_offre text,
    motif_modification_caracteristique_appel_offre text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone,
    parent_id uuid,
    is_active_caracteristique_appel_offre boolean,
    duree_estimee_jours_caracteristique_appel_offre numeric
);


ALTER TABLE public.caracteristiques_appels_offres OWNER TO postgres;

--
-- TOC entry 5413 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.appel_offre_id IS 'Identifiant unique de l''appel d''offres.';


--
-- TOC entry 5414 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.version_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.version_caracteristique_appel_offre IS 'Version du critère pour le suivi des modifications.';


--
-- TOC entry 5415 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.date_demarrage_prevue_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.date_demarrage_prevue_caracteristique_appel_offre IS 'Date prévue de démarrage des travaux.';


--
-- TOC entry 5416 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.date_livraison_previsionnelle_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.date_livraison_previsionnelle_caracteristique_appel_offre IS 'Date prévue de livraison des travaux.';


--
-- TOC entry 5417 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.lieu_execution_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.lieu_execution_caracteristique_appel_offre IS 'Lieu prévu pour l''exécution des travaux.';


--
-- TOC entry 5418 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.penalites_retard_journalier_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.penalites_retard_journalier_caracteristique_appel_offre IS 'Montant de pénalité par jour de retard (ex: 50 000 FCFA/jour). Dissuasif.';


--
-- TOC entry 5419 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.montant_garantie_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.montant_garantie_caracteristique_appel_offre IS 'Caution de bonne exécution (souvent 5-10% du marché).';


--
-- TOC entry 5420 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.delai_garantie_jours_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.delai_garantie_jours_caracteristique_appel_offre IS 'Durée de garantie après réception (ex: 365 jours).';


--
-- TOC entry 5421 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.conditions_paiement_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.conditions_paiement_caracteristique_appel_offre IS 'Modalités (ex: 30% avance, 40% mi-parcours, 30% livraison).';


--
-- TOC entry 5422 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.modalites_execution_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.modalites_execution_caracteristique_appel_offre IS 'Exigences particulières.';


--
-- TOC entry 5423 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.documents_requis_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.documents_requis_caracteristique_appel_offre IS 'Liste des pièces à fournir (ex: [Attestation fiscale, Assurance, Caution]).';


--
-- TOC entry 5424 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.autres_informations_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.autres_informations_caracteristique_appel_offre IS 'Infos diverses.';


--
-- TOC entry 5425 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.motif_modification_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.motif_modification_caracteristique_appel_offre IS 'Pourquoi cette modification (ex: Demande du maître d''ouvrage, Erreur initiale, Force majeure).';


--
-- TOC entry 5426 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.parent_id IS 'Identifiant du critère parent, si applicable.';


--
-- TOC entry 5427 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN caracteristiques_appels_offres.duree_estimee_jours_caracteristique_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.caracteristiques_appels_offres.duree_estimee_jours_caracteristique_appel_offre IS 'Durée estimée des travaux en jours.';


--
-- TOC entry 234 (class 1259 OID 38432)
-- Name: criteres_evaluations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.criteres_evaluations (
    id_critere_evaluation uuid NOT NULL,
    lot_id uuid NOT NULL,
    numero_critere_evaluation character varying(20),
    libelle_critere_evaluation character varying(160),
    description_critere_evaluation text,
    note_reference_critere_evaluation numeric(8,2),
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
-- TOC entry 5428 (class 0 OID 0)
-- Dependencies: 234
-- Name: COLUMN criteres_evaluations.lot_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.criteres_evaluations.lot_id IS 'Identifiant du lot associé.';


--
-- TOC entry 235 (class 1259 OID 38561)
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
-- TOC entry 237 (class 1259 OID 39892)
-- Name: evaluations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.evaluations (
    id_evaluation uuid NOT NULL,
    appel_offre_id uuid NOT NULL,
    lot_id uuid NOT NULL,
    prestataire_id uuid NOT NULL,
    numero_evaluation character varying(50) NOT NULL,
    date_evaluation timestamp(0) without time zone,
    statut_evaluation smallint DEFAULT '0'::smallint NOT NULL,
    note_totale numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    note_maximale numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    pourcentage_final numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    rang integer,
    commentaire_general text,
    recommandation text,
    documents_evalues json,
    evaluateur_principal_id uuid,
    date_validation timestamp(0) without time zone,
    valide_par uuid,
    motif_rejet text,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.evaluations OWNER TO postgres;

--
-- TOC entry 5429 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.appel_offre_id IS 'Identifiant de l''appel d''offres.';


--
-- TOC entry 5430 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.lot_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.lot_id IS 'Identifiant du lot évalué.';


--
-- TOC entry 5431 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.prestataire_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.prestataire_id IS 'Identifiant du prestataire évalué.';


--
-- TOC entry 5432 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.numero_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.numero_evaluation IS 'Numéro unique de l''évaluation';


--
-- TOC entry 5433 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.date_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.date_evaluation IS 'Date de réalisation de l''évaluation';


--
-- TOC entry 5434 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.statut_evaluation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.statut_evaluation IS '0=En attente, 1=En cours, 2=Terminée, 3=Validée, 4=Rejetée';


--
-- TOC entry 5435 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.note_totale; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.note_totale IS 'Note totale obtenue';


--
-- TOC entry 5436 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.note_maximale; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.note_maximale IS 'Note maximale possible';


--
-- TOC entry 5437 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.pourcentage_final; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.pourcentage_final IS 'Pourcentage final (note_totale/note_maximale * 100)';


--
-- TOC entry 5438 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.rang; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.rang IS 'Rang parmi tous les prestataires évalués pour ce lot';


--
-- TOC entry 5439 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.commentaire_general; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.commentaire_general IS 'Commentaire général sur l''évaluation';


--
-- TOC entry 5440 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.recommandation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.recommandation IS 'Recommandation pour l''attribution';


--
-- TOC entry 5441 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.documents_evalues; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.documents_evalues IS 'Liste des documents consultés pour l''évaluation';


--
-- TOC entry 5442 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.evaluateur_principal_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.evaluateur_principal_id IS 'Identifiant de l''évaluateur principal';


--
-- TOC entry 5443 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.date_validation; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.date_validation IS 'Date de validation de l''évaluation';


--
-- TOC entry 5444 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.valide_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.valide_par IS 'Identifiant de l''utilisateur ayant validé';


--
-- TOC entry 5445 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.motif_rejet; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.motif_rejet IS 'Motif en cas de rejet de l''évaluation';


--
-- TOC entry 5446 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.created_by IS 'Identifiant de l''utilisateur créateur';


--
-- TOC entry 5447 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.updated_by IS 'Identifiant de l''utilisateur modificateur';


--
-- TOC entry 5448 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.deleted_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.deleted_by IS 'Identifiant de l''utilisateur suppresseur';


--
-- TOC entry 5449 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.created_at IS 'Date de création';


--
-- TOC entry 5450 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.updated_at IS 'Date de mise à jour';


--
-- TOC entry 5451 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN evaluations.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.evaluations.deleted_at IS 'Date de suppression logique';


--
-- TOC entry 244 (class 1259 OID 40228)
-- Name: evaluations_lots_prestataires; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.evaluations_lots_prestataires (
    critere_evaluation_id uuid NOT NULL,
    evaluation_id uuid NOT NULL,
    prestatiare_id uuid NOT NULL,
    created_by uuid NOT NULL,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.evaluations_lots_prestataires OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 40054)
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
-- TOC entry 224 (class 1259 OID 38106)
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
-- TOC entry 233 (class 1259 OID 38390)
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
    version_lot integer,
    CONSTRAINT lots_attribution_lot_check CHECK (((attribution_lot)::text = ANY ((ARRAY['0'::character varying, '1'::character varying])::text[]))),
    CONSTRAINT lots_statut_lol_check CHECK (((statut_lot)::text = ANY ((ARRAY['0'::character varying, '1'::character varying])::text[])))
);


ALTER TABLE public.lots OWNER TO postgres;

--
-- TOC entry 5452 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN lots.appel_offre_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.lots.appel_offre_id IS 'Identifiant de l''appel d''offres associé.';


--
-- TOC entry 5453 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN lots.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.lots.parent_id IS 'Identifiant du lot principal associé.';


--
-- TOC entry 220 (class 1259 OID 34844)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 34843)
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
-- TOC entry 5454 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 242 (class 1259 OID 40086)
-- Name: paiements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.paiements (
    id_paiement uuid NOT NULL,
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
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.paiements OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 38097)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 38138)
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
-- TOC entry 5455 (class 0 OID 0)
-- Dependencies: 226
-- Name: TABLE permissions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.permissions IS 'Table des permissions du système de contrôle daccès';


--
-- TOC entry 5456 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.name IS 'Nom affiché de la permission';


--
-- TOC entry 5457 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.slug; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.slug IS 'Identifiant unique pour la permission';


--
-- TOC entry 5458 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.description; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.description IS 'Description détaillée de la permission';


--
-- TOC entry 5459 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.resource; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.resource IS 'Entité/ressource concernée (ex: users, posts)';


--
-- TOC entry 5460 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.action; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.action IS 'Action autorisée sur la ressource';


--
-- TOC entry 5461 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.guard_name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.guard_name IS 'Guard utilisé (web, api, etc.)';


--
-- TOC entry 5462 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.category; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.category IS 'Catégorie de permission pour groupement';


--
-- TOC entry 5463 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.priority; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.priority IS 'Priorité de la permission (0-255)';


--
-- TOC entry 5464 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.is_active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.is_active IS 'Permission active/inactive';


--
-- TOC entry 5465 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.is_system; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.is_system IS 'Permission système (non modifiable)';


--
-- TOC entry 5466 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.conditions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.conditions IS 'Conditions supplémentaires en JSON';


--
-- TOC entry 5467 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.created_by IS 'Membres qui a créé la permission';


--
-- TOC entry 5468 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.updated_by IS 'Dernier membres ayant modifié';


--
-- TOC entry 5469 (class 0 OID 0)
-- Dependencies: 226
-- Name: COLUMN permissions.last_used_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.permissions.last_used_at IS 'Dernière utilisation de la permission';


--
-- TOC entry 225 (class 1259 OID 38123)
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
-- TOC entry 236 (class 1259 OID 39854)
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
-- TOC entry 5470 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN prestataires.numero_cc_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.numero_cc_prestataire IS 'Numéro de la carte de contribuable';


--
-- TOC entry 5471 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN prestataires.numero_rccm_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.numero_rccm_prestataire IS 'Numéro du Registre de Commerce et du Crédit Mobilier';


--
-- TOC entry 5472 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN prestataires.telephone_principal_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.telephone_principal_prestataire IS 'Numéro de téléphone principal du prestataire';


--
-- TOC entry 5473 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN prestataires.telephone_secondaire_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.telephone_secondaire_prestataire IS 'Numéro de téléphone secondaire du prestataire';


--
-- TOC entry 5474 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN prestataires.adresse_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.adresse_prestataire IS 'Adresse physique du prestataire';


--
-- TOC entry 5475 (class 0 OID 0)
-- Dependencies: 236
-- Name: COLUMN prestataires.representant_legal_prestataire; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires.representant_legal_prestataire IS 'Informations sur le représentant légal au format JSON (tableau de represents): id, statut, nom, prenoms, contact, email, nationalité, pays, adresse, profession, date_naissance, lieu_naissance, numero_piece_identite, type_piece_identite, date_delivrance, lieu_delivrance, date_expiration.';


--
-- TOC entry 243 (class 1259 OID 40174)
-- Name: prestataires_lots; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prestataires_lots (
    prestataire_id uuid NOT NULL,
    lot_id uuid NOT NULL,
    proforma_id uuid NOT NULL,
    date_debut_reelle date,
    date_fin_reelle date,
    statut_attribution smallint DEFAULT '0'::smallint NOT NULL,
    motif_suspension text,
    date_suspension timestamp(0) without time zone,
    motif_retrait text,
    date_retrait timestamp(0) without time zone,
    jours_retard integer DEFAULT 0 NOT NULL,
    penalites_appliquees numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    pourcentage_avancement numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    observations text,
    created_by uuid,
    updated_by uuid,
    deleted_by uuid,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.prestataires_lots OWNER TO postgres;

--
-- TOC entry 5476 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.date_debut_reelle; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_debut_reelle IS 'Date réelle de début des travaux';


--
-- TOC entry 5477 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.date_fin_reelle; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_fin_reelle IS 'Date réelle de fin des travaux';


--
-- TOC entry 5478 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.statut_attribution; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.statut_attribution IS '0=En attente, 1=Attribué, 2=Suspendu, 3=Retiré, 4=Terminé';


--
-- TOC entry 5479 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.motif_suspension; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.motif_suspension IS 'Raison de la suspension des travaux';


--
-- TOC entry 5480 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.date_suspension; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_suspension IS 'Date de suspension';


--
-- TOC entry 5481 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.motif_retrait; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.motif_retrait IS 'Raison du retrait du lot au prestataire';


--
-- TOC entry 5482 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.date_retrait; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.date_retrait IS 'Date de retrait';


--
-- TOC entry 5483 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.jours_retard; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.jours_retard IS 'Nombre de jours de retard accumulés';


--
-- TOC entry 5484 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.penalites_appliquees; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.penalites_appliquees IS 'Montant total des pénalités appliquées';


--
-- TOC entry 5485 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.pourcentage_avancement; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.pourcentage_avancement IS 'Pourcentage d''avancement des travaux (0-100)';


--
-- TOC entry 5486 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.observations; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.observations IS 'Observations et notes sur l''exécution';


--
-- TOC entry 5487 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.created_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.created_by IS 'Utilisateur ayant créé l''attribution';


--
-- TOC entry 5488 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.updated_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.updated_by IS 'Utilisateur ayant mis à jour';


--
-- TOC entry 5489 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.deleted_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.deleted_by IS 'Utilisateur ayant retiré/supprimé';


--
-- TOC entry 5490 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.created_at IS 'Date de création de l''attribution';


--
-- TOC entry 5491 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.updated_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.updated_at IS 'Date de dernière mise à jour';


--
-- TOC entry 5492 (class 0 OID 0)
-- Dependencies: 243
-- Name: COLUMN prestataires_lots.deleted_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.prestataires_lots.deleted_at IS 'Date de suppression logique (retrait)';


--
-- TOC entry 232 (class 1259 OID 38344)
-- Name: proformas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.proformas (
    id_proforma uuid NOT NULL,
    version_proforma integer DEFAULT 1 NOT NULL,
    date_proforma date,
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
    parent_id uuid,
    numero_proforma character varying,
    date_fin_validee_proforma date,
    date_debut_validee_proforma date,
    date_redemarrage_proforma date
);


ALTER TABLE public.proformas OWNER TO postgres;

--
-- TOC entry 5493 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.version_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.version_proforma IS 'Version du critère pour le suivi des modifications.';


--
-- TOC entry 5494 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.date_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.date_proforma IS 'Date de création de la proforma.';


--
-- TOC entry 5495 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.modalite_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.modalite_proforma IS 'Modalités de paiement spécifiées dans la proforma.';


--
-- TOC entry 5496 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.penalites_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.penalites_proforma IS 'Pénalités associées à la proforma.';


--
-- TOC entry 5497 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.motif_modification_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.motif_modification_proforma IS 'Pourquoi cette modification (ex: Demande du maître d''ouvrage, Erreur initiale, Force majeure).';


--
-- TOC entry 5498 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.actif_proforma; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.actif_proforma IS 'Permet de désactiver temporairement une proforma sans la supprimer.';


--
-- TOC entry 5499 (class 0 OID 0)
-- Dependencies: 232
-- Name: COLUMN proformas.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.proformas.parent_id IS 'Identifiant du critère parent, si applicable.';


--
-- TOC entry 227 (class 1259 OID 38178)
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
-- TOC entry 5500 (class 0 OID 0)
-- Dependencies: 227
-- Name: TABLE role_permissions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.role_permissions IS 'Table pivot : association entre rôles et permissions';


--
-- TOC entry 5501 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.role_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.role_id IS 'ID du rôle';


--
-- TOC entry 5502 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.permission_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.permission_id IS 'ID de la permission';


--
-- TOC entry 5503 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.attribue_par; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.attribue_par IS 'ID de l''utilisateur qui a attribué cette permission au rôle';


--
-- TOC entry 5504 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.attribue_le; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.attribue_le IS 'Date et heure d''attribution de la permission';


--
-- TOC entry 5505 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.expire_le; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.expire_le IS 'Date d''expiration (pour permissions temporaires)';


--
-- TOC entry 5506 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.actif; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.actif IS 'Permission active pour ce rôle';


--
-- TOC entry 5507 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.conditions; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.conditions IS 'Conditions spécifiques pour cette attribution';


--
-- TOC entry 5508 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN role_permissions.notes; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.notes IS 'Notes sur l''attribution de cette permission';


--
-- TOC entry 221 (class 1259 OID 38041)
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
-- TOC entry 228 (class 1259 OID 38228)
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
-- TOC entry 240 (class 1259 OID 40022)
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
-- TOC entry 229 (class 1259 OID 38233)
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
-- TOC entry 5509 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.libelle_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.libelle_type_appel_offre IS 'Libellé du type d''appel d''offres';


--
-- TOC entry 5510 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.code_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.code_type_appel_offre IS 'Code court (ex: AOT, AOS, AOF). Utilisé dans les numéros d''AO.';


--
-- TOC entry 5511 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.valeur_minimuim_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.valeur_minimuim_type_appel_offre IS 'Valeur minimale associée au type d''appel d''offres';


--
-- TOC entry 5512 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.valeur_maximuim_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.valeur_maximuim_type_appel_offre IS 'Valeur maximale associée au type d''appel d''offres';


--
-- TOC entry 5513 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.description_critere_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.description_critere_type_appel_offre IS 'Description détaillée du type d''appel d''offres';


--
-- TOC entry 5514 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.actif_type_appel_offre; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.actif_type_appel_offre IS 'Permet de désactiver temporairement un type sans le supprimer.';


--
-- TOC entry 5515 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN types_appels_offres.parent_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.types_appels_offres.parent_id IS 'Identifiant du critère parent, si applicable.';


--
-- TOC entry 222 (class 1259 OID 38058)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id uuid NOT NULL,
    nom_complet character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    telephone_principal character varying(255),
    telepone_secondaire character varying(255),
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
-- TOC entry 4956 (class 2604 OID 34847)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 5378 (class 0 OID 40269)
-- Dependencies: 245
-- Data for Name: alertes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.alertes (id, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5363 (class 0 OID 38265)
-- Dependencies: 230
-- Data for Name: appels_offres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.appels_offres (id_appel_offre, type_appel_offre_id, numero_appel_offre, libelle_critere_appel_offre, objet_critere_appel_offre, montant_global_appel_offre, description_critere_critere_appel_offre, date_publication_critere_appel_offre, date_limite_depot_critere_appel_offre, date_ouverture_plis_critere_appel_offre, statut_evaluation_critere_appel_offre, conditions_participation_critere_appel_offre, criteres_selection_critere_appel_offre, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a074ffd0-5ed6-487d-a23b-2cae9705a1c2	a073c14b-af1f-4b76-8cf4-0d5b8fb28dc7	AOR-2025-001	CONSTRUCTION D'UNISE NORD D'ABIDJAN	Parfait ! J'ai généré les 4 vues Blade pour la gestion des appels d'offres :	23000000.00	Parfait ! J'ai généré les 4 vues Blade pour la gestion des appels d'offres :	2025-07-20 00:00:00	2026-01-11 00:00:00	2026-03-26 00:00:00	1	Parfait ! J'ai généré les 4 vues Blade pour la gestion des appels d'offres :	Parfait ! J'ai généré les 4 vues Blade pour la gestion des appels d'offres :	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-11-27 11:41:24	2025-11-27 14:45:54	\N
a0858f55-3910-4b00-9321-2d8360199c13	a0858897-bf8b-4260-8f5d-9079fcd64bc6	AOCO-2025-001	AZERTY 002	Objet de l'Appel d'Offres	9750000.00	Description Détaillée	2025-12-05 00:00:00	2025-12-15 00:00:00	2026-01-31 00:00:00	1	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-05 17:15:58	2025-12-05 17:15:58	\N
a08d4b60-4b5a-4f05-add1-9a1c2e419603	a08d4852-293b-475b-9291-17db5ae56e51	DRP-2025-001	ACHAT DE METERIELS SCOLAIRE	Acquisition de matériels et fournitures scolaires dans le cadre du Programme de Soutien à l’Éducation\r\nFourniture, livraison et mise à disposition de matériels scolaires pour les établissements d’enseignement\r\nMarché relatif à l’achat et à la livraison de matériels scolaires pour le Ministère de l'Éducation Nationale	450000.00	La description détaillée porte sur l’acquisition de matériels scolaires destinés aux établissements d’enseignement. Le marché comprend la fourniture de fournitures diverses (cahiers, stylos, marqueurs, ardoises, kits géométriques…), d’équipements pédagogiques (tableaux blancs, cartes murales, supports didactiques) ainsi que de tout matériel spécifié dans le cahier des charges. Les produits doivent être neufs, conformes aux standards de qualité, livrés dans les délais prévus et répartis selon la liste des destinations fournies par l’autorité contractante.	2025-12-10 00:00:00	2025-12-25 00:00:00	2026-02-06 00:00:00	1	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-09 13:32:34	2025-12-09 13:32:34	\N
\.


--
-- TOC entry 5371 (class 0 OID 39960)
-- Dependencies: 238
-- Data for Name: banques; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.banques (id_banque, prestataire_id, nom_banque, code_banque, numero_compte_banque, code_guichet_banque, cle_rib_banque, iban_banque, swift_bic_banque, titulaire_compte_banque, actif_banque, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0855cc9-c02a-4359-a275-21d0868c0104	a0834807-6769-4a7c-a6c3-dcc72acd9442	ECOBANK	CI000	01012022521	01001	50	CI008010010101202252151	SGBFCIAB851	SEMINO CISSOCO	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	2025-12-05 14:54:38	2025-12-05 14:55:32	2025-12-05 14:55:32
a0855805-b520-46b3-8183-1052a934bf74	a0834807-6769-4a7c-a6c3-dcc72acd9442	ECOBANK	CI008	01012022521	01001	50	CI008010010101202252150	SGBFCIAB855	SEMINO CISSOCO	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-05 14:41:18	2025-12-05 14:57:07	\N
a085573a-2011-4e45-be2d-7506511f5428	a0834807-6769-4a7c-a6c3-dcc72acd9442	ECOBANK	CI000	01012022525	01002	50	CI008010010101202252151	SGBFCIAB851	SEMINO CISSOCO	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-05 14:39:05	2025-12-05 14:57:17	\N
\.


--
-- TOC entry 5372 (class 0 OID 39991)
-- Dependencies: 239
-- Data for Name: capacites_techniques; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.capacites_techniques (id_capacite_technique, prestataire_id, effectif_permanent_capacite_technique, effectif_temporaire_capacite_technique, moyens_materiels_capacite_technique, certifications_capacite_technique, agrements_capacite_technique, references_capacite_technique, competences_cles_capacite_technique, domaines_expertise_capacite_technique, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5364 (class 0 OID 38305)
-- Dependencies: 231
-- Data for Name: caracteristiques_appels_offres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.caracteristiques_appels_offres (id_caracteristique_appel_offre, appel_offre_id, version_caracteristique_appel_offre, date_demarrage_prevue_caracteristique_appel_offre, date_livraison_previsionnelle_caracteristique_appel_offre, lieu_execution_caracteristique_appel_offre, penalites_retard_journalier_caracteristique_appel_offre, montant_garantie_caracteristique_appel_offre, delai_garantie_jours_caracteristique_appel_offre, conditions_paiement_caracteristique_appel_offre, modalites_execution_caracteristique_appel_offre, documents_requis_caracteristique_appel_offre, autres_informations_caracteristique_appel_offre, motif_modification_caracteristique_appel_offre, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id, is_active_caracteristique_appel_offre, duree_estimee_jours_caracteristique_appel_offre) FROM stdin;
a07b4f6b-a35a-4e33-9d0d-030a913ead7d	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	3	2025-11-29	2026-01-28	Yamoussoukro	1000000.00	15000000.00	365.00	Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement	Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Motif de modification *	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-11-30 14:58:58	2025-11-30 14:58:58	\N	a07b3aff-8ec1-4bc5-9187-8d86280925a6	f	\N
a07b4fa0-e087-4496-9ce2-c8eeca39c598	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	4	2025-11-29	2026-01-28	Yamoussoukro	1000000.00	15000000.00	\N	Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement	Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Motif de modification *	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-11-30 14:59:33	2025-12-01 09:19:25	\N	a07b3aff-8ec1-4bc5-9187-8d86280925a6	f	60
a07cd8fd-ce3f-4cc3-b753-b1d04f23f5c7	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	5	2025-12-29	2026-12-28	Yamoussoukro	1000000.00	15000000.00	\N	Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement	Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Motif de modification *	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-01 09:19:30	2025-12-01 09:19:30	\N	a07b4fa0-e087-4496-9ce2-c8eeca39c598	t	364
a07b3aff-8ec1-4bc5-9187-8d86280925a6	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	1	2026-01-11	2028-12-29	Yamoussoukro	1000000.00	15000000.00	\N	Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement	Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-11-30 14:01:52	2025-11-30 14:17:14	\N	\N	f	\N
a07b407e-a6b1-4008-b716-318f69c3e252	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	2	2025-12-30	2028-12-15	Yamoussoukro	1000000.00	15000000.00	\N	Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement Conditions de paiement	Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution Modalités d'exécution	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Documents requis Documents requis Documents requis Documents requis Documents requis Documents requis	Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification Motif de modification	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-11-30 14:17:14	2025-11-30 14:17:14	\N	a07b3aff-8ec1-4bc5-9187-8d86280925a6	f	\N
\.


--
-- TOC entry 5367 (class 0 OID 38432)
-- Dependencies: 234
-- Data for Name: criteres_evaluations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.criteres_evaluations (id_critere_evaluation, lot_id, numero_critere_evaluation, libelle_critere_evaluation, description_critere_evaluation, note_reference_critere_evaluation, statut_critere_evaluation, ordre_execution_critere_evaluation, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a07d4c2c-291f-4f40-b857-10973421606e	a07d172c-db69-4535-ae63-a6eeae44253f	CRIT-001	Qualité Technique de l'offre	Pour formater ta durée avec Carbon, il faut d’abord comprendre que :\r\n\r\nduree_estimee_jours_caracteristique_appel_offre semble être un nombre de jours, pas une date.\r\n\r\ndate() n’est pas adapté ici → il attend un timestamp.\r\n\r\nAvec Carbon, tu peux soit afficher le nombre de jours, soit le convertir en intervalle (ex : “3 jours”).\r\n\r\nVoici les bonnes approches :	70.00	1	1	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-01 14:41:33	2025-12-01 14:48:52	\N
a07d4f33-b410-4fa6-a11b-7c842af4cba5	a07d172c-db69-4535-ae63-a6eeae44253f	CRIT-002	Qualité Morale de prestataire	Si tu veux, je peux :\r\n\r\nte donner le dégradé exact que tu veux (couleurs/angle) ;\r\n\r\ngénérer une classe réutilisable pour ton projet ;\r\n\r\ncréer un dégradé animé plus discret pour la production.\r\n\r\nTu veux quel style (soft / vibrant / sombre / image + overlay / animé) ?	30.00	1	2	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-01 14:50:01	2025-12-01 14:51:29	\N
\.


--
-- TOC entry 5368 (class 0 OID 38561)
-- Dependencies: 235
-- Data for Name: documents; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.documents (id_document, lot_id, type_document, titre_document, fichier_nom_document, fichier_path_document, fichier_type_document, fichier_taille_document, description_document, date_document, version_document, est_valide_document, valide_par, valide_at, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5370 (class 0 OID 39892)
-- Dependencies: 237
-- Data for Name: evaluations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.evaluations (id_evaluation, appel_offre_id, lot_id, prestataire_id, numero_evaluation, date_evaluation, statut_evaluation, note_totale, note_maximale, pourcentage_final, rang, commentaire_general, recommandation, documents_evalues, evaluateur_principal_id, date_validation, valide_par, motif_rejet, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5377 (class 0 OID 40228)
-- Dependencies: 244
-- Data for Name: evaluations_lots_prestataires; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.evaluations_lots_prestataires (critere_evaluation_id, evaluation_id, prestatiare_id, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5374 (class 0 OID 40054)
-- Dependencies: 241
-- Data for Name: evaluations_prestataires; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.evaluations_prestataires (id_evaluation_prestataire, prestataire_id, note_qualification_evaluation_prestataire, date_derniere_evaluation_evaluation_prestataire, nombre_contrats_executes_evaluation_prestataire, taux_respect_delais_evaluation_prestataire, taux_qualite_evaluation_prestataire, nombre_litiges_evaluation_prestataire, liste_statut_evaluation_prestataire, date_mise_en_liste_evaluation_prestataire, date_fin_sanction_evaluation_prestataire, motif_liste_noire_evaluation_prestataire, commentaire_evaluation_prestataire, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5358 (class 0 OID 38106)
-- Dependencies: 224
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5366 (class 0 OID 38390)
-- Dependencies: 233
-- Data for Name: lots; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lots (id_lot, appel_offre_id, numero, libelle, description_critere, specifications_techniques, motif_retrait, date_attribution, date_debut_prevue, date_fin_prevue, date_retrait, attribution_lot, statut_lot, taux_penalites, statut_retrait, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id, version_lot) FROM stdin;
a0773903-f6df-4c49-931e-0c6fc7eb98a5	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	LOT-AOR-AOR-2025-001-001	ESPECTION ET APPLATISSEMENT DU TERRE	Pour renseigner automatiquement la version du lot comme étant la plus grande version existante + 1, voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	\N	\N	2025-05-30 14:11:00	2027-11-28 14:12:00	\N	0	0	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-11-28 14:13:01	2025-12-01 11:47:50	\N	\N	\N
a07d16c1-a8e9-4a2a-9795-b3a7c5f7f3c5	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	LOT-AOR-AOR-2025-001-001	ESPECTION ET APPLATISSEMENT DU TERRE	Pour renseigner automatiquement la version du lot comme étant la plus grande version existante + 1, voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	Alternance	\N	2025-05-30 00:00:00	2027-10-28 00:00:00	\N	0	0	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-01 12:12:11	2025-12-01 12:12:11	\N	a0773903-f6df-4c49-931e-0c6fc7eb98a5	\N
a08f3299-e1e4-4063-9249-a32af25a8a47	a08d4b60-4b5a-4f05-add1-9a1c2e419603	LOT-DRP-DRP-2025-001-001	CONSTRUCTION D'AUTO-ROUTE	La construction d’une autoroute est un projet d’infrastructure stratégique visant à aménager une voie de circulation rapide, sécurisée et durable, destinée à faciliter le transport des personnes et des marchandises. Elle implique un ensemble d’études techniques, d’opérations de génie civil et d’aménagements complémentaires, réalisés selon des normes strictes en matière de qualité, de sécurité et d’impact environnemental.\r\n\r\n1. Études préalables\r\n\r\nAvant le démarrage des travaux, plusieurs analyses sont menées :\r\n\r\nÉtudes topographiques, géologiques et hydrologiques\r\n\r\nÉtudes de trafic et de mobilité\r\n\r\nÉtudes d’impact environnemental et social\r\n\r\nDéfinition du tracé optimal et des ouvrages d’art nécessaires\r\n\r\nCes études permettent de s’assurer de la viabilité du projet et de définir les solutions techniques adéquates.\r\n\r\n2. Travaux de terrassement\r\n\r\nLa construction commence par :\r\n\r\nLe décapage et le nettoyage de la zone\r\n\r\nLe nivellement du terrain\r\n\r\nLe remblayage et la stabilisation des sols\r\n\r\nLe traitement des zones sensibles (marécages, reliefs escarpés, terrains instables)\r\n\r\nCes travaux constituent la base sur laquelle reposera toute l'infrastructure.\r\n\r\n3. Ouvrages d’art\r\n\r\nLes ouvrages d’art sont essentiels pour assurer la continuité et la sécurité de la route :\r\n\r\nPonts et viaducs\r\n\r\nPassages supérieurs et inférieurs\r\n\r\nDalots, ouvrages hydrauliques et bassins de rétention\r\n\r\nPassages pour la faune\r\n\r\nIls permettent de franchir les obstacles naturels et d'assurer une bonne gestion des eaux.\r\n\r\n4. Structure de la chaussée\r\n\r\nUne autoroute comporte plusieurs couches successives :\r\n\r\nCouche de fondation\r\n\r\nCouche de base\r\n\r\nCouche de roulement en béton bitumineux ou béton de ciment\r\n\r\nCes couches sont dimensionnées pour supporter un trafic intense et lourd sur plusieurs décennies.\r\n\r\n5. Aménagements complémentaires\r\n\r\nPour garantir fonctionnalité et sécurité, divers équipements sont installés :\r\n\r\nPostes de péage\r\n\r\nÉclairage public\r\n\r\nDispositifs de sécurité (glissières, panneaux, marquage au sol)\r\n\r\nAires de repos et stations-service\r\n\r\nRéseaux de drainage et d’assainissement\r\n\r\nCes aménagements améliorent le confort des usagers et prolongent la durée de vie de l’infrastructure.\r\n\r\n6. Contrôle qualité et sécurité\r\n\r\nPendant toute la durée du chantier, des contrôles sont effectués :\r\n\r\nTests de compactage et de portance\r\n\r\nContrôle des matériaux\r\n\r\nVérification du respect des normes techniques\r\n\r\nLa sécurité des travailleurs et des usagers est également une priorité.\r\n\r\n7. Mise en service\r\n\r\nUne fois les travaux terminés, l’autoroute est ouverte à la circulation après :\r\n\r\nInspections finales\r\n\r\nEssais techniques\r\n\r\nInstallation de la signalisation définitive	1. Données générales\r\n\r\nType d’infrastructure : Autoroute à 2 × 2 voies (extensible à 2 × 3 voies selon trafic).\r\n\r\nLargeur d’une voie : 3,50 m.\r\n\r\nLargeur de la bande d’arrêt d’urgence : 2,50 à 3,00 m.\r\n\r\nAccotements : 1,50 m à 2,00 m.\r\n\r\nVitesse de référence : 120 km/h (ou selon normes nationales).\r\n\r\nEmprise minimale : 60 à 80 mètres selon le tracé.\r\n\r\n2. Terrassements et plateforme routière\r\n2.1 Études géotechniques\r\n\r\nIdentification des sols, classification, essais CBR, essais Proctor.\r\n\r\nTraitement des zones instables par compactage dynamique, géotextiles, drains verticaux, remblais légers, etc.\r\n\r\n2.2 Travaux de terrassement\r\n\r\nDéblai/remblai selon profil en travers.\r\n\r\nCompaction par couches ≤ 30 cm.\r\n\r\nTaux de compactage requis : ≥ 95 % Proctor Modifié.\r\n\r\nTraitement des sols avec chaux/ciment pour améliorer portance si nécessaire.\r\n\r\n3. Structure de chaussée\r\n3.1 Couche de fondation\r\n\r\nMatériaux graveleux naturels ou traités.\r\n\r\nÉpaisseur : 20 à 30 cm selon trafic et nature du sol.\r\n\r\n3.2 Couche de base\r\n\r\nGrave-bitume, grave-ciment, ou matériaux concassés haute performance.\r\n\r\nÉpaisseur : 15 à 25 cm.\r\n\r\n3.3 Couche de roulement\r\n\r\nBéton bitumineux (BBM, BBSG, BET) ou béton de ciment fond dur.\r\n\r\nÉpaisseur : 5 à 8 cm selon type de revêtement.\r\n\r\nMacrotexture contrôlée pour assurer l’adhérence.\r\n\r\n3.4 Drainage\r\n\r\nFossés latéraux, caniveaux, buses, dalots, collecteurs.\r\n\r\nBassins de rétention pour zones à risque d’inondation.\r\n\r\nÉtanchéité assurée dans zones sensibles.\r\n\r\n4. Ouvrages d’art\r\n4.1 Ouvrages courants\r\n\r\nPonts, viaducs, passages supérieurs et inférieurs.\r\n\r\nDalles et tabliers en béton armé ou précontraint.\r\n\r\nNormes de charge : Eurocode ou normes nationales équivalentes.\r\n\r\n4.2 Ouvrages hydrauliques\r\n\r\nDalots (1 à 5 cellules), buses circulaires, radiers.\r\n\r\nDimensionnement basé sur pluies décennales ou centennales selon importance.\r\n\r\n5. Sécurité et équipements de l’autoroute\r\n5.1 Signalisation\r\n\r\nSignalisation verticale : panneaux rétro-réfléchissants classe II ou III.\r\n\r\nSignalisation horizontale : peinture thermoplastique ou résine froide.\r\n\r\nMarquage guide, STOP, zébras, bandes vibrantes.\r\n\r\n5.2 Barrières de sécurité\r\n\r\nGlissières métalliques ou béton armé type « New Jersey ».\r\n\r\nNormes de résistance : EN 1317 ou équivalentes.\r\n\r\n5.3 Éclairage\r\n\r\nÉclairage LED dans zones sensibles : échangeurs, péages, tunnels.\r\n\r\nMâts de 12 à 30 m selon besoin.\r\n\r\n6. Aires et installations annexes\r\n6.1 Aires de repos\r\n\r\nParkings VL et poids lourds.\r\n\r\nBloc sanitaire, espace vert, poubelles, éclairage.\r\n\r\n6.2 Postes de péage\r\n\r\nVoies manuelles, automatiques, télépéage.\r\n\r\nSystème de pesage dynamique et contrôle vidéo.\r\n\r\n6.3 Ouvrages de gestion de l’eau\r\n\r\nBassins de rétention\r\n\r\nFossés bétonnés\r\n\r\nRéseaux d’assainissement\r\n\r\n7. Contrôle qualité\r\n\r\nEssais granulométriques, CBR, Los Angeles, Proctor.\r\n\r\nEssais de densité in situ (noyau, pénétromètre).\r\n\r\nContrôle de l’adhérence, déflexion, régularité (I.R.I).\r\n\r\nVérification de l’épaisseur et de la température des couches bitumineuses.\r\n\r\n8. Normes et références\r\n\r\nNormes routières nationales (ouest-africaines / CEDEAO / CI).\r\n\r\nEurocodes pour ouvrages en béton et acier.\r\n\r\nManuels techniques des travaux publics.\r\n\r\nRecommandations SETRA, CEBTP, ASTM, AASHTO.	\N	\N	2025-12-19 12:10:00	2026-03-01 12:10:00	\N	0	0	0.50	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-10 12:14:58	2025-12-10 12:18:36	\N	\N	\N
a07d172c-db69-4535-ae63-a6eeae44253f	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	LOT-AOR-AOR-2025-001-001	ESPECTION ET APPLATISSEMENT DU TERRE	Pour renseigner automatiquement la version du lot comme étant la plus grande version existante + 1, voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	SDSDD	2025-12-04	2025-05-30 00:00:00	2027-10-28 00:00:00	2025-12-04	1	1	5.00	1	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	2025-12-01 12:13:21	2025-12-04 16:37:12	\N	a07d16c1-a8e9-4a2a-9795-b3a7c5f7f3c5	\N
a07d0e0c-aa49-4488-b2fe-136f24256e8a	a074ffd0-5ed6-487d-a23b-2cae9705a1c2	LOT-AOR-AOR-2025-001-001	ESPECTION ET APPLATISSEMENT DU TERRE	Pour renseigner automatiquement la version du lot comme étant la plus grande version existante + 1, voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	voici la manière propre et robuste de le faire dans ton contrôleur Laravel.	Pourquoi modifiez-vous ce lot ? *	2025-12-04	2025-01-09 00:00:00	2025-12-28 00:00:00	\N	1	1	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-01 11:47:50	2025-12-04 16:41:26	\N	a0773903-f6df-4c49-931e-0c6fc7eb98a5	\N
a08f33e8-ef5f-40a2-b004-6041bdfc2da0	a08d4b60-4b5a-4f05-add1-9a1c2e419603	LOT-DRP-DRP-2025-001-001	CONSTRUCTION D'AUTO-ROUTE	La construction d’une autoroute est un projet d’infrastructure stratégique visant à aménager une voie de circulation rapide, sécurisée et durable, destinée à faciliter le transport des personnes et des marchandises. Elle implique un ensemble d’études techniques, d’opérations de génie civil et d’aménagements complémentaires, réalisés selon des normes strictes en matière de qualité, de sécurité et d’impact environnemental.\r\n\r\n1. Études préalables\r\n\r\nAvant le démarrage des travaux, plusieurs analyses sont menées :\r\n\r\nÉtudes topographiques, géologiques et hydrologiques\r\n\r\nÉtudes de trafic et de mobilité\r\n\r\nÉtudes d’impact environnemental et social\r\n\r\nDéfinition du tracé optimal et des ouvrages d’art nécessaires\r\n\r\nCes études permettent de s’assurer de la viabilité du projet et de définir les solutions techniques adéquates.\r\n\r\n2. Travaux de terrassement\r\n\r\nLa construction commence par :\r\n\r\nLe décapage et le nettoyage de la zone\r\n\r\nLe nivellement du terrain\r\n\r\nLe remblayage et la stabilisation des sols\r\n\r\nLe traitement des zones sensibles (marécages, reliefs escarpés, terrains instables)\r\n\r\nCes travaux constituent la base sur laquelle reposera toute l'infrastructure.\r\n\r\n3. Ouvrages d’art\r\n\r\nLes ouvrages d’art sont essentiels pour assurer la continuité et la sécurité de la route :\r\n\r\nPonts et viaducs\r\n\r\nPassages supérieurs et inférieurs\r\n\r\nDalots, ouvrages hydrauliques et bassins de rétention\r\n\r\nPassages pour la faune\r\n\r\nIls permettent de franchir les obstacles naturels et d'assurer une bonne gestion des eaux.\r\n\r\n4. Structure de la chaussée\r\n\r\nUne autoroute comporte plusieurs couches successives :\r\n\r\nCouche de fondation\r\n\r\nCouche de base\r\n\r\nCouche de roulement en béton bitumineux ou béton de ciment\r\n\r\nCes couches sont dimensionnées pour supporter un trafic intense et lourd sur plusieurs décennies.\r\n\r\n5. Aménagements complémentaires\r\n\r\nPour garantir fonctionnalité et sécurité, divers équipements sont installés :\r\n\r\nPostes de péage\r\n\r\nÉclairage public\r\n\r\nDispositifs de sécurité (glissières, panneaux, marquage au sol)\r\n\r\nAires de repos et stations-service\r\n\r\nRéseaux de drainage et d’assainissement\r\n\r\nCes aménagements améliorent le confort des usagers et prolongent la durée de vie de l’infrastructure.\r\n\r\n6. Contrôle qualité et sécurité\r\n\r\nPendant toute la durée du chantier, des contrôles sont effectués :\r\n\r\nTests de compactage et de portance\r\n\r\nContrôle des matériaux\r\n\r\nVérification du respect des normes techniques\r\n\r\nLa sécurité des travailleurs et des usagers est également une priorité.\r\n\r\n7. Mise en service\r\n\r\nUne fois les travaux terminés, l’autoroute est ouverte à la circulation après :\r\n\r\nInspections finales\r\n\r\nEssais techniques\r\n\r\nInstallation de la signalisation définitive	1. Données générales\r\n\r\nType d’infrastructure : Autoroute à 2 × 2 voies (extensible à 2 × 3 voies selon trafic).\r\n\r\nLargeur d’une voie : 3,50 m.\r\n\r\nLargeur de la bande d’arrêt d’urgence : 2,50 à 3,00 m.\r\n\r\nAccotements : 1,50 m à 2,00 m.\r\n\r\nVitesse de référence : 120 km/h (ou selon normes nationales).\r\n\r\nEmprise minimale : 60 à 80 mètres selon le tracé.\r\n\r\n2. Terrassements et plateforme routière\r\n2.1 Études géotechniques\r\n\r\nIdentification des sols, classification, essais CBR, essais Proctor.\r\n\r\nTraitement des zones instables par compactage dynamique, géotextiles, drains verticaux, remblais légers, etc.\r\n\r\n2.2 Travaux de terrassement\r\n\r\nDéblai/remblai selon profil en travers.\r\n\r\nCompaction par couches ≤ 30 cm.\r\n\r\nTaux de compactage requis : ≥ 95 % Proctor Modifié.\r\n\r\nTraitement des sols avec chaux/ciment pour améliorer portance si nécessaire.\r\n\r\n3. Structure de chaussée\r\n3.1 Couche de fondation\r\n\r\nMatériaux graveleux naturels ou traités.\r\n\r\nÉpaisseur : 20 à 30 cm selon trafic et nature du sol.\r\n\r\n3.2 Couche de base\r\n\r\nGrave-bitume, grave-ciment, ou matériaux concassés haute performance.\r\n\r\nÉpaisseur : 15 à 25 cm.\r\n\r\n3.3 Couche de roulement\r\n\r\nBéton bitumineux (BBM, BBSG, BET) ou béton de ciment fond dur.\r\n\r\nÉpaisseur : 5 à 8 cm selon type de revêtement.\r\n\r\nMacrotexture contrôlée pour assurer l’adhérence.\r\n\r\n3.4 Drainage\r\n\r\nFossés latéraux, caniveaux, buses, dalots, collecteurs.\r\n\r\nBassins de rétention pour zones à risque d’inondation.\r\n\r\nÉtanchéité assurée dans zones sensibles.\r\n\r\n4. Ouvrages d’art\r\n4.1 Ouvrages courants\r\n\r\nPonts, viaducs, passages supérieurs et inférieurs.\r\n\r\nDalles et tabliers en béton armé ou précontraint.\r\n\r\nNormes de charge : Eurocode ou normes nationales équivalentes.\r\n\r\n4.2 Ouvrages hydrauliques\r\n\r\nDalots (1 à 5 cellules), buses circulaires, radiers.\r\n\r\nDimensionnement basé sur pluies décennales ou centennales selon importance.\r\n\r\n5. Sécurité et équipements de l’autoroute\r\n5.1 Signalisation\r\n\r\nSignalisation verticale : panneaux rétro-réfléchissants classe II ou III.\r\n\r\nSignalisation horizontale : peinture thermoplastique ou résine froide.\r\n\r\nMarquage guide, STOP, zébras, bandes vibrantes.\r\n\r\n5.2 Barrières de sécurité\r\n\r\nGlissières métalliques ou béton armé type « New Jersey ».\r\n\r\nNormes de résistance : EN 1317 ou équivalentes.\r\n\r\n5.3 Éclairage\r\n\r\nÉclairage LED dans zones sensibles : échangeurs, péages, tunnels.\r\n\r\nMâts de 12 à 30 m selon besoin.\r\n\r\n6. Aires et installations annexes\r\n6.1 Aires de repos\r\n\r\nParkings VL et poids lourds.\r\n\r\nBloc sanitaire, espace vert, poubelles, éclairage.\r\n\r\n6.2 Postes de péage\r\n\r\nVoies manuelles, automatiques, télépéage.\r\n\r\nSystème de pesage dynamique et contrôle vidéo.\r\n\r\n6.3 Ouvrages de gestion de l’eau\r\n\r\nBassins de rétention\r\n\r\nFossés bétonnés\r\n\r\nRéseaux d’assainissement\r\n\r\n7. Contrôle qualité\r\n\r\nEssais granulométriques, CBR, Los Angeles, Proctor.\r\n\r\nEssais de densité in situ (noyau, pénétromètre).\r\n\r\nContrôle de l’adhérence, déflexion, régularité (I.R.I).\r\n\r\nVérification de l’épaisseur et de la température des couches bitumineuses.\r\n\r\n8. Normes et références\r\n\r\nNormes routières nationales (ouest-africaines / CEDEAO / CI).\r\n\r\nEurocodes pour ouvrages en béton et acier.\r\n\r\nManuels techniques des travaux publics.\r\n\r\nRecommandations SETRA, CEBTP, ASTM, AASHTO.	Réajustement du contenu technique du lot afin de mieux correspondre aux besoins réels du projet.\r\nAjout ou suppression de certaines prestations pour garantir la cohérence technique avec les autres lots.	\N	2025-12-19 00:00:00	2026-03-01 00:00:00	\N	0	0	0.50	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-10 12:18:36	2025-12-10 12:19:04	\N	a08f3299-e1e4-4063-9249-a32af25a8a47	\N
a08593ef-3837-40da-b7ef-1ba5a175cbba	a0858f55-3910-4b00-9321-2d8360199c13	LOT-AOCO-AOCO-2025-001-001	CONSTRUCTION D'UN DISPENSAIRE	Description	Spécifications techniques	\N	2025-12-05	\N	\N	\N	1	1	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-05 17:28:50	2025-12-05 17:38:59	\N	\N	\N
a08f2538-7d8e-400d-a348-7e3888eea9df	a0858f55-3910-4b00-9321-2d8360199c13	LOT-AOCO-AOCO-2025-001-002	LOT 2	Description	Spécifications techniques	\N	\N	2025-12-11 00:00:00	2026-05-30 00:00:00	\N	0	1	\N	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-10 11:37:35	2025-12-10 11:37:35	\N	\N	\N
a08f3413-ed41-4e79-8e6d-b4748b027746	a08d4b60-4b5a-4f05-add1-9a1c2e419603	LOT-DRP-DRP-2025-001-001	CONSTRUCTION D'AUTO-ROUTE	a construction d’une autoroute est un projet d’infrastructure stratégique visant à aménager une voie de circulation rapide, sécurisée et durable, destinée à faciliter le transport des personnes et des marchandises. Elle implique un ensemble d’études techniques, d’opérations de génie civil et d’aménagements complémentaires, réalisés selon des normes strictes en matière de qualité, de sécurité et d’impact environnemental.\r\n\r\n1. Études préalables\r\n\r\nAvant le démarrage des travaux, plusieurs analyses sont menées :\r\n\r\nÉtudes topographiques, géologiques et hydrologiques\r\n\r\nÉtudes de trafic et de mobilité\r\n\r\nÉtudes d’impact environnemental et social\r\n\r\nDéfinition du tracé optimal et des ouvrages d’art nécessaires\r\n\r\nCes études permettent de s’assurer de la viabilité du projet et de définir les solutions techniques adéquates.\r\n\r\n2. Travaux de terrassement\r\n\r\nLa construction commence par :\r\n\r\nLe décapage et le nettoyage de la zone\r\n\r\nLe nivellement du terrain\r\n\r\nLe remblayage et la stabilisation des sols\r\n\r\nLe traitement des zones sensibles (marécages, reliefs escarpés, terrains instables)\r\n\r\nCes travaux constituent la base sur laquelle reposera toute l'infrastructure.\r\n\r\n3. Ouvrages d’art\r\n\r\nLes ouvrages d’art sont essentiels pour assurer la continuité et la sécurité de la route :\r\n\r\nPonts et viaducs\r\n\r\nPassages supérieurs et inférieurs\r\n\r\nDalots, ouvrages hydrauliques et bassins de rétention\r\n\r\nPassages pour la faune\r\n\r\nIls permettent de franchir les obstacles naturels et d'assurer une bonne gestion des eaux.\r\n\r\n4. Structure de la chaussée\r\n\r\nUne autoroute comporte plusieurs couches successives :\r\n\r\nCouche de fondation\r\n\r\nCouche de base\r\n\r\nCouche de roulement en béton bitumineux ou béton de ciment\r\n\r\nCes couches sont dimensionnées pour supporter un trafic intense et lourd sur plusieurs décennies.\r\n\r\n5. Aménagements complémentaires\r\n\r\nPour garantir fonctionnalité et sécurité, divers équipements sont installés :\r\n\r\nPostes de péage\r\n\r\nÉclairage public\r\n\r\nDispositifs de sécurité (glissières, panneaux, marquage au sol)\r\n\r\nAires de repos et stations-service\r\n\r\nRéseaux de drainage et d’assainissement\r\n\r\nCes aménagements améliorent le confort des usagers et prolongent la durée de vie de l’infrastructure.\r\n\r\n6. Contrôle qualité et sécurité\r\n\r\nPendant toute la durée du chantier, des contrôles sont effectués :\r\n\r\nTests de compactage et de portance\r\n\r\nContrôle des matériaux\r\n\r\nVérification du respect des normes techniques\r\n\r\nLa sécurité des travailleurs et des usagers est également une priorité.\r\n\r\n7. Mise en service\r\n\r\nUne fois les travaux terminés, l’autoroute est ouverte à la circulation après :\r\n\r\nInspections finales\r\n\r\nEssais techniques\r\n\r\nInstallation de la signalisation définitive	1. Données générales\r\n\r\nType d’infrastructure : Autoroute à 2 × 2 voies (extensible à 2 × 3 voies selon trafic).\r\n\r\nLargeur d’une voie : 3,50 m.\r\n\r\nLargeur de la bande d’arrêt d’urgence : 2,50 à 3,00 m.\r\n\r\nAccotements : 1,50 m à 2,00 m.\r\n\r\nVitesse de référence : 120 km/h (ou selon normes nationales).\r\n\r\nEmprise minimale : 60 à 80 mètres selon le tracé.\r\n\r\n2. Terrassements et plateforme routière\r\n2.1 Études géotechniques\r\n\r\nIdentification des sols, classification, essais CBR, essais Proctor.\r\n\r\nTraitement des zones instables par compactage dynamique, géotextiles, drains verticaux, remblais légers, etc.\r\n\r\n2.2 Travaux de terrassement\r\n\r\nDéblai/remblai selon profil en travers.\r\n\r\nCompaction par couches ≤ 30 cm.\r\n\r\nTaux de compactage requis : ≥ 95 % Proctor Modifié.\r\n\r\nTraitement des sols avec chaux/ciment pour améliorer portance si nécessaire.\r\n\r\n3. Structure de chaussée\r\n3.1 Couche de fondation\r\n\r\nMatériaux graveleux naturels ou traités.\r\n\r\nÉpaisseur : 20 à 30 cm selon trafic et nature du sol.\r\n\r\n3.2 Couche de base\r\n\r\nGrave-bitume, grave-ciment, ou matériaux concassés haute performance.\r\n\r\nÉpaisseur : 15 à 25 cm.\r\n\r\n3.3 Couche de roulement\r\n\r\nBéton bitumineux (BBM, BBSG, BET) ou béton de ciment fond dur.\r\n\r\nÉpaisseur : 5 à 8 cm selon type de revêtement.\r\n\r\nMacrotexture contrôlée pour assurer l’adhérence.\r\n\r\n3.4 Drainage\r\n\r\nFossés latéraux, caniveaux, buses, dalots, collecteurs.\r\n\r\nBassins de rétention pour zones à risque d’inondation.\r\n\r\nÉtanchéité assurée dans zones sensibles.\r\n\r\n4. Ouvrages d’art\r\n4.1 Ouvrages courants\r\n\r\nPonts, viaducs, passages supérieurs et inférieurs.\r\n\r\nDalles et tabliers en béton armé ou précontraint.\r\n\r\nNormes de charge : Eurocode ou normes nationales équivalentes.\r\n\r\n4.2 Ouvrages hydrauliques\r\n\r\nDalots (1 à 5 cellules), buses circulaires, radiers.\r\n\r\nDimensionnement basé sur pluies décennales ou centennales selon importance.\r\n\r\n5. Sécurité et équipements de l’autoroute\r\n5.1 Signalisation\r\n\r\nSignalisation verticale : panneaux rétro-réfléchissants classe II ou III.\r\n\r\nSignalisation horizontale : peinture thermoplastique ou résine froide.\r\n\r\nMarquage guide, STOP, zébras, bandes vibrantes.\r\n\r\n5.2 Barrières de sécurité\r\n\r\nGlissières métalliques ou béton armé type « New Jersey ».\r\n\r\nNormes de résistance : EN 1317 ou équivalentes.\r\n\r\n5.3 Éclairage\r\n\r\nÉclairage LED dans zones sensibles : échangeurs, péages, tunnels.\r\n\r\nMâts de 12 à 30 m selon besoin.\r\n\r\n6. Aires et installations annexes\r\n6.1 Aires de repos\r\n\r\nParkings VL et poids lourds.\r\n\r\nBloc sanitaire, espace vert, poubelles, éclairage.\r\n\r\n6.2 Postes de péage\r\n\r\nVoies manuelles, automatiques, télépéage.\r\n\r\nSystème de pesage dynamique et contrôle vidéo.\r\n\r\n6.3 Ouvrages de gestion de l’eau\r\n\r\nBassins de rétention\r\n\r\nFossés bétonnés\r\n\r\nRéseaux d’assainissement\r\n\r\n7. Contrôle qualité\r\n\r\nEssais granulométriques, CBR, Los Angeles, Proctor.\r\n\r\nEssais de densité in situ (noyau, pénétromètre).\r\n\r\nContrôle de l’adhérence, déflexion, régularité (I.R.I).\r\n\r\nVérification de l’épaisseur et de la température des couches bitumineuses.\r\n\r\n8. Normes et références\r\n\r\nNormes routières nationales (ouest-africaines / CEDEAO / CI).\r\n\r\nEurocodes pour ouvrages en béton et acier.\r\n\r\nManuels techniques des travaux publics.\r\n\r\nRecommandations SETRA, CEBTP, ASTM, AASHTO.	éajustement du contenu technique du lot afin de mieux correspondre aux besoins réels du projet.\r\nAjout ou suppression de certaines prestations pour garantir la cohérence technique avec les autres lots.	\N	2025-12-19 00:00:00	2026-03-01 00:00:00	\N	0	0	0.50	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-10 12:19:04	2025-12-10 12:19:32	\N	a08f33e8-ef5f-40a2-b004-6041bdfc2da0	\N
a08f343d-b95a-436c-aa3f-8d322b64aa03	a08d4b60-4b5a-4f05-add1-9a1c2e419603	LOT-DRP-DRP-2025-001-001	CONSTRUCTION D'AUTO-ROUTE	La construction d’une autoroute est un projet d’infrastructure stratégique visant à aménager une voie de circulation rapide, sécurisée et durable, destinée à faciliter le transport des personnes et des marchandises. Elle implique un ensemble d’études techniques, d’opérations de génie civil et d’aménagements complémentaires, réalisés selon des normes strictes en matière de qualité, de sécurité et d’impact environnemental.\r\n\r\n1. Études préalables\r\n\r\nAvant le démarrage des travaux, plusieurs analyses sont menées :\r\n\r\nÉtudes topographiques, géologiques et hydrologiques\r\n\r\nÉtudes de trafic et de mobilité\r\n\r\nÉtudes d’impact environnemental et social\r\n\r\nDéfinition du tracé optimal et des ouvrages d’art nécessaires\r\n\r\nCes études permettent de s’assurer de la viabilité du projet et de définir les solutions techniques adéquates.\r\n\r\n2. Travaux de terrassement\r\n\r\nLa construction commence par :\r\n\r\nLe décapage et le nettoyage de la zone\r\n\r\nLe nivellement du terrain\r\n\r\nLe remblayage et la stabilisation des sols\r\n\r\nLe traitement des zones sensibles (marécages, reliefs escarpés, terrains instables)\r\n\r\nCes travaux constituent la base sur laquelle reposera toute l'infrastructure.\r\n\r\n3. Ouvrages d’art\r\n\r\nLes ouvrages d’art sont essentiels pour assurer la continuité et la sécurité de la route :\r\n\r\nPonts et viaducs\r\n\r\nPassages supérieurs et inférieurs\r\n\r\nDalots, ouvrages hydrauliques et bassins de rétention\r\n\r\nPassages pour la faune\r\n\r\nIls permettent de franchir les obstacles naturels et d'assurer une bonne gestion des eaux.\r\n\r\n4. Structure de la chaussée\r\n\r\nUne autoroute comporte plusieurs couches successives :\r\n\r\nCouche de fondation\r\n\r\nCouche de base\r\n\r\nCouche de roulement en béton bitumineux ou béton de ciment\r\n\r\nCes couches sont dimensionnées pour supporter un trafic intense et lourd sur plusieurs décennies.\r\n\r\n5. Aménagements complémentaires\r\n\r\nPour garantir fonctionnalité et sécurité, divers équipements sont installés :\r\n\r\nPostes de péage\r\n\r\nÉclairage public\r\n\r\nDispositifs de sécurité (glissières, panneaux, marquage au sol)\r\n\r\nAires de repos et stations-service\r\n\r\nRéseaux de drainage et d’assainissement\r\n\r\nCes aménagements améliorent le confort des usagers et prolongent la durée de vie de l’infrastructure.\r\n\r\n6. Contrôle qualité et sécurité\r\n\r\nPendant toute la durée du chantier, des contrôles sont effectués :\r\n\r\nTests de compactage et de portance\r\n\r\nContrôle des matériaux\r\n\r\nVérification du respect des normes techniques\r\n\r\nLa sécurité des travailleurs et des usagers est également une priorité.\r\n\r\n7. Mise en service\r\n\r\nUne fois les travaux terminés, l’autoroute est ouverte à la circulation après :\r\n\r\nInspections finales\r\n\r\nEssais techniques\r\n\r\nInstallation de la signalisation définitive	1. Données générales\r\n\r\nType d’infrastructure : Autoroute à 2 × 2 voies (extensible à 2 × 3 voies selon trafic).\r\n\r\nLargeur d’une voie : 3,50 m.\r\n\r\nLargeur de la bande d’arrêt d’urgence : 2,50 à 3,00 m.\r\n\r\nAccotements : 1,50 m à 2,00 m.\r\n\r\nVitesse de référence : 120 km/h (ou selon normes nationales).\r\n\r\nEmprise minimale : 60 à 80 mètres selon le tracé.\r\n\r\n2. Terrassements et plateforme routière\r\n2.1 Études géotechniques\r\n\r\nIdentification des sols, classification, essais CBR, essais Proctor.\r\n\r\nTraitement des zones instables par compactage dynamique, géotextiles, drains verticaux, remblais légers, etc.\r\n\r\n2.2 Travaux de terrassement\r\n\r\nDéblai/remblai selon profil en travers.\r\n\r\nCompaction par couches ≤ 30 cm.\r\n\r\nTaux de compactage requis : ≥ 95 % Proctor Modifié.\r\n\r\nTraitement des sols avec chaux/ciment pour améliorer portance si nécessaire.\r\n\r\n3. Structure de chaussée\r\n3.1 Couche de fondation\r\n\r\nMatériaux graveleux naturels ou traités.\r\n\r\nÉpaisseur : 20 à 30 cm selon trafic et nature du sol.\r\n\r\n3.2 Couche de base\r\n\r\nGrave-bitume, grave-ciment, ou matériaux concassés haute performance.\r\n\r\nÉpaisseur : 15 à 25 cm.\r\n\r\n3.3 Couche de roulement\r\n\r\nBéton bitumineux (BBM, BBSG, BET) ou béton de ciment fond dur.\r\n\r\nÉpaisseur : 5 à 8 cm selon type de revêtement.\r\n\r\nMacrotexture contrôlée pour assurer l’adhérence.\r\n\r\n3.4 Drainage\r\n\r\nFossés latéraux, caniveaux, buses, dalots, collecteurs.\r\n\r\nBassins de rétention pour zones à risque d’inondation.\r\n\r\nÉtanchéité assurée dans zones sensibles.\r\n\r\n4. Ouvrages d’art\r\n4.1 Ouvrages courants\r\n\r\nPonts, viaducs, passages supérieurs et inférieurs.\r\n\r\nDalles et tabliers en béton armé ou précontraint.\r\n\r\nNormes de charge : Eurocode ou normes nationales équivalentes.\r\n\r\n4.2 Ouvrages hydrauliques\r\n\r\nDalots (1 à 5 cellules), buses circulaires, radiers.\r\n\r\nDimensionnement basé sur pluies décennales ou centennales selon importance.\r\n\r\n5. Sécurité et équipements de l’autoroute\r\n5.1 Signalisation\r\n\r\nSignalisation verticale : panneaux rétro-réfléchissants classe II ou III.\r\n\r\nSignalisation horizontale : peinture thermoplastique ou résine froide.\r\n\r\nMarquage guide, STOP, zébras, bandes vibrantes.\r\n\r\n5.2 Barrières de sécurité\r\n\r\nGlissières métalliques ou béton armé type « New Jersey ».\r\n\r\nNormes de résistance : EN 1317 ou équivalentes.\r\n\r\n5.3 Éclairage\r\n\r\nÉclairage LED dans zones sensibles : échangeurs, péages, tunnels.\r\n\r\nMâts de 12 à 30 m selon besoin.\r\n\r\n6. Aires et installations annexes\r\n6.1 Aires de repos\r\n\r\nParkings VL et poids lourds.\r\n\r\nBloc sanitaire, espace vert, poubelles, éclairage.\r\n\r\n6.2 Postes de péage\r\n\r\nVoies manuelles, automatiques, télépéage.\r\n\r\nSystème de pesage dynamique et contrôle vidéo.\r\n\r\n6.3 Ouvrages de gestion de l’eau\r\n\r\nBassins de rétention\r\n\r\nFossés bétonnés\r\n\r\nRéseaux d’assainissement\r\n\r\n7. Contrôle qualité\r\n\r\nEssais granulométriques, CBR, Los Angeles, Proctor.\r\n\r\nEssais de densité in situ (noyau, pénétromètre).\r\n\r\nContrôle de l’adhérence, déflexion, régularité (I.R.I).\r\n\r\nVérification de l’épaisseur et de la température des couches bitumineuses.\r\n\r\n8. Normes et références\r\n\r\nNormes routières nationales (ouest-africaines / CEDEAO / CI).\r\n\r\nEurocodes pour ouvrages en béton et acier.\r\n\r\nManuels techniques des travaux publics.\r\n\r\nRecommandations SETRA, CEBTP, ASTM, AASHTO.	éajustement du contenu technique du lot afin de mieux correspondre aux besoins réels du projet.\r\nAjout ou suppression de certaines prestations pour garantir la cohérence technique avec les autres lots.	2025-12-10	2025-12-19 00:00:00	2026-03-01 00:00:00	\N	1	1	0.50	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-10 12:19:32	2025-12-10 15:47:47	\N	a08f3413-ed41-4e79-8e6d-b4748b027746	\N
\.


--
-- TOC entry 5354 (class 0 OID 34844)
-- Dependencies: 220
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
99	2014_10_11_000000_create_roles_table	1
100	2014_10_12_000000_create_users_table	1
101	2014_10_12_100000_create_password_reset_tokens_table	1
102	2019_08_19_000000_create_failed_jobs_table	1
103	2019_12_14_000001_create_personal_access_tokens_table	1
104	2025_11_20_114959_create_permissions_table	1
105	2025_11_20_115028_create_role_permissions_table	1
106	2025_11_20_122235_create_type_appel_offres_table	1
107	2025_11_20_122355_create_appels_offres_table	1
108	2025_11_20_122424_create_caracteristique_appel_offres_table	1
109	2025_11_20_122457_create_proformas_table	1
110	2025_11_20_122459_create_lots_table	1
111	2025_11_20_122612_create_critere_evaluations_table	1
114	2025_11_20_151640_create_documents_table	1
125	2025_11_20_151611_create_prestataires_table	2
126	2025_11_20_151612_create_evaluations_table	2
127	2025_11_20_151714_create_banques_table	2
128	2025_11_20_151753_create_capacite_techniques_table	2
129	2025_11_20_151819_create_situation_financieres_table	2
130	2025_11_20_151900_create_evaluation_prestataires_table	2
131	2025_11_20_152740_create_paiements_table	2
133	2025_11_20_152919_create_prestataires_lots_table	3
134	2025_11_20_152947_create_evaluations_lots_prestataires_table	3
135	2025_11_20_153204_create_alertes_table	3
136	2025_12_08_153200_creat_fixe_colonnes_table	4
\.


--
-- TOC entry 5375 (class 0 OID 40086)
-- Dependencies: 242
-- Data for Name: paiements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.paiements (id_paiement, banque_id, montant_net_paye_paiement, statut_paiement, date_validation_paiement, motif_rejet_paiement, observations_paiement, valide_par, paye_par, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5357 (class 0 OID 38097)
-- Dependencies: 223
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 5360 (class 0 OID 38138)
-- Dependencies: 226
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.permissions (id, name, slug, description, resource, action, guard_name, category, priority, is_active, is_system, conditions, created_by, updated_by, last_used_at, created_at, updated_at, deleted_at) FROM stdin;
a073b928-4fa2-4cf5-b047-a29458de63f7	Gérer les utilisateurs	users-manage	Permet de gérer toutes les actions sur les utilisateurs	users	manage	web	Gestion des utilisateurs	20	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5540-406f-b3c5-caa55f8040bc	Créer des utilisateurs	users-create	Permet de créer de nouveaux utilisateurs	users	create	web	Gestion des utilisateurs	10	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-566d-4c57-b419-2771c263c868	Voir les utilisateurs	users-read	Permet de consulter la liste des utilisateurs	users	read	web	Gestion des utilisateurs	5	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5773-4533-967f-44bb1b0c08a0	Modifier les utilisateurs	users-update	Permet de modifier les informations des utilisateurs	users	update	web	Gestion des utilisateurs	10	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-580a-424b-bda3-7351a6eedbf0	Supprimer les utilisateurs	users-delete	Permet de supprimer des utilisateurs	users	delete	web	Gestion des utilisateurs	15	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-58ae-429e-9a35-6e0a7f017ca6	Exporter les utilisateurs	users-export	Permet d'exporter la liste des utilisateurs	users	export	web	Gestion des utilisateurs	5	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-590f-44ff-8825-78a7a3d07a14	Importer des utilisateurs	users-import	Permet d'importer des utilisateurs depuis un fichier	users	import	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5962-47d9-b665-d1f61cc24508	Valider les utilisateurs	users-validate	Permet de valider les comptes utilisateurs	users	validate	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-59b0-4863-b37e-aef368b69df7	Rejecter les utilisateurs	users-reject	Permet de rejetter les comptes utilisateurs	users	reject	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5a02-4fdb-b3a9-d0793908453f	Restaurer les utilisateurs	users-restore	Permet de restaurer les comptes utilisateurs supprimés	users	restore	web	Gestion des utilisateurs	15	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5a4f-4f51-b030-0f40f8f61a30	Dupliquer les utilisateurs	users-duplicate	Permet de dupliquer les comptes utilisateurs	users	duplicate	web	Gestion des utilisateurs	10	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5a9b-4d1b-a862-3674e2dcf4b7	Télécharger les utilisateurs	users-download	Permet de télécharger les informations des utilisateurs	users	download	web	Gestion des utilisateurs	5	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5af2-4472-92f7-e1f4109acd81	Créer des rôles	roles-create	Permet de créer de nouveaux rôles	roles	create	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5b41-4e63-9609-f30466286c73	Voir les rôles	roles-read	Permet de consulter la liste des rôles	roles	read	web	Gestion des rôles	5	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5b8e-4338-a83f-0cc32398ff7f	Modifier les rôles	roles-update	Permet de modifier les rôles	roles	update	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5bda-4772-b185-c0bc00bd8611	Supprimer les rôles	roles-delete	Permet de supprimer des rôles	roles	delete	web	Gestion des rôles	15	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5cb1-4157-9835-637092de810c	Assigner des rôles	roles-assign	Permet d'assigner des rôles aux utilisateurs	roles	update	web	Gestion des rôles	15	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5dbd-4f0b-8f4f-8110cd1bccc1	Exporter les rôles	roles-export	Permet d'exporter la liste des rôles	roles	export	web	Gestion des rôles	5	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5e5c-4b8c-a199-11b82a1aa80e	Importer des rôles	roles-import	Permet d'importer des rôles depuis un fichier	roles	import	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5ee9-46bf-96d1-f8153045db4a	Dupliquer les rôles	roles-duplicate	Permet de dupliquer les rôles	roles	duplicate	web	Gestion des rôles	10	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-5f6d-4f9d-a02e-3994da4d7d7c	Télécharger les rôles	roles-download	Permet de télécharger les informations des rôles	roles	download	web	Gestion des rôles	5	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6025-4b2a-afb0-a0f690daa6ba	Restaurer les rôles	roles-restore	Permet de restaurer les rôles supprimés	roles	restore	web	Gestion des rôles	15	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-60a4-42fc-b03b-5c609ea038a1	Gérer les rôles	roles-manage	Permet de gérer toutes les actions sur les rôles	roles	manage	web	Gestion des rôles	20	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6126-41b3-97de-c2c4526e502b	Gérer les permissions	permissions-manage	Permet de gérer toutes les permissions	permissions	manage	web	Gestion des permissions	20	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-61a6-4d0f-b901-42e86e07d1a5	Voir les permissions	permissions-read	Permet de consulter les permissions	permissions	read	web	Gestion des permissions	5	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6229-462e-93dd-53e0a48d911f	Assigner des permissions	permissions-assign	Permet d'assigner des permissions aux rôles	permissions	update	web	Gestion des permissions	15	t	t	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-62c8-4fb8-af2b-39960ba2583a	Voir le dashboard	dashboard-read	Permet d'accéder au tableau de bord	dashboard	read	web	Dashboard	1	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6384-4319-9d02-ec3c18f5f4ec	Voir les rapports	reports-read	Permet de consulter les rapports	reports	read	web	Rapports	5	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-63eb-46c8-85f9-fb5563759003	Exporter les rapports	reports-export	Permet d'exporter les rapports	reports	export	web	Rapports	5	t	f	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
\.


--
-- TOC entry 5359 (class 0 OID 38123)
-- Dependencies: 225
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5369 (class 0 OID 39854)
-- Dependencies: 236
-- Data for Name: prestataires; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prestataires (id_prestataire, raison_sociale_prestataire, numero_identification_prestataire, email_prestataire, numero_cc_prestataire, numero_rccm_prestataire, telephone_principal_prestataire, telephone_secondaire_prestataire, adresse_prestataire, ville_prestataire, pays_prestataire, representant_legal_prestataire, statut_prestataire, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0834807-6769-4a7c-a6c3-dcc72acd9442	Société de Construction ABC	CI-U123456789	nfcdjobo@gmail.com	CC-696332655	RCCM-A-2020-B-12345	+2250140940330	+2250245785524	Abidjan Cocody La Djibi	Cocody, Abidjan, Cote D'Ivoire	Côte d'Ivoire	[{"nom":"N'DRI DJOBO","contact":"+225 07 07 07 07 07","email":"djobo@yopmail.com","nationalite":"Ivoirienne","pays":"C\\u00f4te d\\u2019Ivoire","adresse":"Abidjan Cocody La Djibi","profession":"Ing\\u00e9nieur en b\\u00e2timent","date_naissance":"1990-12-04","lieu_naissance":"TAABOU","numero_piece_identite":"CI012345678901","type_piece_identite":"CNI","date_delivrance":"2019-12-04","lieu_delivrance":"Abidjan","date_expiration":"2030-06-04","id":"25818495-1ac3-4e7b-86e3-63f231bb0764","statut":1,"created_at":"2025-12-04T14:04:56+00:00"}]	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-04 14:04:56	2025-12-04 14:04:56	\N
\.


--
-- TOC entry 5376 (class 0 OID 40174)
-- Dependencies: 243
-- Data for Name: prestataires_lots; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prestataires_lots (prestataire_id, lot_id, proforma_id, date_debut_reelle, date_fin_reelle, statut_attribution, motif_suspension, date_suspension, motif_retrait, date_retrait, jours_retard, penalites_appliquees, pourcentage_avancement, observations, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a0834807-6769-4a7c-a6c3-dcc72acd9442	a07d172c-db69-4535-ae63-a6eeae44253f	a0834649-d89a-4ef7-a22c-6e95a4810b19	2025-12-04	\N	1	\N	\N	\N	\N	0	0.00	0.00	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-04 16:35:20	2025-12-04 16:35:20	\N
a0834807-6769-4a7c-a6c3-dcc72acd9442	a07d0e0c-aa49-4488-b2fe-136f24256e8a	a0834649-d89a-4ef7-a22c-6e95a4810b19	2025-12-04	\N	1	\N	\N	\N	\N	0	0.00	0.00	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-04 16:41:26	2025-12-04 16:41:26	\N
a0834807-6769-4a7c-a6c3-dcc72acd9442	a08593ef-3837-40da-b7ef-1ba5a175cbba	a0834649-d89a-4ef7-a22c-6e95a4810b19	2025-12-05	\N	1	\N	\N	\N	\N	0	0.00	0.00	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-05 17:38:59	2025-12-05 17:38:59	\N
a0834807-6769-4a7c-a6c3-dcc72acd9442	a08f343d-b95a-436c-aa3f-8d322b64aa03	a08f7eb8-2f52-4aab-8f73-afc9647077aa	2025-12-10	\N	1	\N	\N	\N	\N	0	0.00	0.00	\N	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-10 15:47:47	2025-12-10 15:47:47	\N
\.


--
-- TOC entry 5365 (class 0 OID 38344)
-- Dependencies: 232
-- Data for Name: proformas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.proformas (id_proforma, version_proforma, date_proforma, montant_retenu_proforma, taxe_montant, remise_montant_proforma, modalite_proforma, penalites_proforma, motif_modification_proforma, actif_proforma, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id, numero_proforma, date_fin_validee_proforma, date_debut_validee_proforma, date_redemarrage_proforma) FROM stdin;
a0834649-d89a-4ef7-a22c-6e95a4810b19	2	2025-12-04	17000000.00	1130500.00	850000.00	Paiement à 30 jours	1000000.00	Motif de modification	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-04 14:00:04	2025-12-04 14:01:03	\N	a08345e0-0ca7-4b99-b2a1-b4c7f4ba1278	PROF-2025-0001	\N	\N	\N
a08345e0-0ca7-4b99-b2a1-b4c7f4ba1278	1	2025-12-04	17000000.00	807500.00	850000.00	Paiement à 30 jours	1000000.00	\N	f	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-04 13:58:54	2025-12-10 13:57:13	\N	\N	PROF-2025-0001	\N	\N	\N
a08f7eb8-2f52-4aab-8f73-afc9647077aa	1	2025-12-10	17000000.00	3060000.00	1190000.00	30% du début, 70% à la livraison	750000.00	\N	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-10 15:47:47	2025-12-10 15:47:47	\N	\N	PROF-2025-0002	2028-02-06	2026-02-06	2026-01-17
\.


--
-- TOC entry 5361 (class 0 OID 38178)
-- Dependencies: 227
-- Data for Name: role_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.role_permissions (role_id, permission_id, attribue_par, attribue_le, expire_le, actif, conditions, notes, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-4fa2-4cf5-b047-a29458de63f7	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5540-406f-b3c5-caa55f8040bc	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-566d-4c57-b419-2771c263c868	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5773-4533-967f-44bb1b0c08a0	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-580a-424b-bda3-7351a6eedbf0	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-58ae-429e-9a35-6e0a7f017ca6	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-590f-44ff-8825-78a7a3d07a14	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5962-47d9-b665-d1f61cc24508	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-59b0-4863-b37e-aef368b69df7	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5a02-4fdb-b3a9-d0793908453f	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5a4f-4f51-b030-0f40f8f61a30	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5a9b-4d1b-a862-3674e2dcf4b7	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5af2-4472-92f7-e1f4109acd81	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5b41-4e63-9609-f30466286c73	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5b8e-4338-a83f-0cc32398ff7f	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5bda-4772-b185-c0bc00bd8611	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5cb1-4157-9835-637092de810c	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5dbd-4f0b-8f4f-8110cd1bccc1	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5e5c-4b8c-a199-11b82a1aa80e	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5ee9-46bf-96d1-f8153045db4a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-5f6d-4f9d-a02e-3994da4d7d7c	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-6025-4b2a-afb0-a0f690daa6ba	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-60a4-42fc-b03b-5c609ea038a1	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-6126-41b3-97de-c2c4526e502b	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-61a6-4d0f-b901-42e86e07d1a5	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-6229-462e-93dd-53e0a48d911f	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-62c8-4fb8-af2b-39960ba2583a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-6384-4319-9d02-ec3c18f5f4ec	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-64f6-48c7-b24f-9a2a3e46cd29	a073b928-63eb-46c8-85f9-fb5563759003	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-4fa2-4cf5-b047-a29458de63f7	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5540-406f-b3c5-caa55f8040bc	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-566d-4c57-b419-2771c263c868	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5773-4533-967f-44bb1b0c08a0	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-580a-424b-bda3-7351a6eedbf0	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-58ae-429e-9a35-6e0a7f017ca6	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-590f-44ff-8825-78a7a3d07a14	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5962-47d9-b665-d1f61cc24508	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-59b0-4863-b37e-aef368b69df7	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5a02-4fdb-b3a9-d0793908453f	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5a4f-4f51-b030-0f40f8f61a30	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5a9b-4d1b-a862-3674e2dcf4b7	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5af2-4472-92f7-e1f4109acd81	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5b41-4e63-9609-f30466286c73	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5b8e-4338-a83f-0cc32398ff7f	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5bda-4772-b185-c0bc00bd8611	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5cb1-4157-9835-637092de810c	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5dbd-4f0b-8f4f-8110cd1bccc1	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5e5c-4b8c-a199-11b82a1aa80e	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5ee9-46bf-96d1-f8153045db4a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-5f6d-4f9d-a02e-3994da4d7d7c	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-6025-4b2a-afb0-a0f690daa6ba	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-60a4-42fc-b03b-5c609ea038a1	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-62c8-4fb8-af2b-39960ba2583a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-6384-4319-9d02-ec3c18f5f4ec	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	a073b928-63eb-46c8-85f9-fb5563759003	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	a073b928-5540-406f-b3c5-caa55f8040bc	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	a073b928-566d-4c57-b419-2771c263c868	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	a073b928-5773-4533-967f-44bb1b0c08a0	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	a073b928-5b41-4e63-9609-f30466286c73	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	a073b928-62c8-4fb8-af2b-39960ba2583a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	a073b928-6384-4319-9d02-ec3c18f5f4ec	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6685-48b8-afba-a5c9cf6870a3	a073b928-566d-4c57-b419-2771c263c868	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6685-48b8-afba-a5c9cf6870a3	a073b928-62c8-4fb8-af2b-39960ba2583a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6685-48b8-afba-a5c9cf6870a3	a073b928-6384-4319-9d02-ec3c18f5f4ec	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-66f0-4446-8703-3f95a54cc955	a073b928-62c8-4fb8-af2b-39960ba2583a	\N	2025-11-26 20:28:01	\N	t	\N	\N	\N	\N	\N	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
\.


--
-- TOC entry 5355 (class 0 OID 38041)
-- Dependencies: 221
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, name, slug, description, level, is_system_role, created_at, updated_at, deleted_at) FROM stdin;
a073b928-64f6-48c7-b24f-9a2a3e46cd29	Super Administrateur	super-admin	Accès complet à toutes les fonctionnalités du système	100	t	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-65a4-43c4-ac0f-5d1af119ac18	Administrateur	admin	Gestion complète du système avec quelques restrictions	80	t	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6619-4129-9dae-a9b671dd94f2	Manager	manager	Gestion des utilisateurs et des contenus	60	f	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-6685-48b8-afba-a5c9cf6870a3	Éditeur	editor	Modification et gestion des contenus	40	f	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
a073b928-66f0-4446-8703-3f95a54cc955	Utilisateur	user	Accès de base au système	20	f	2025-11-26 20:28:01	2025-11-26 20:28:01	\N
\.


--
-- TOC entry 5373 (class 0 OID 40022)
-- Dependencies: 240
-- Data for Name: situations_financieres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.situations_financieres (id_situation_financiere, prestataire_id, exercice_fiscal_situation_financiere, chiffre_affaire_situation_financiere, fonds_propres_situation_financiere, capacite_emprunt_situation_financiere, ratio_solvabilite_situation_financiere, ratio_liquidite_situation_financiere, resultat_net_situation_financiere, total_actif_situation_financiere, total_passif_situation_financiere, observations_situation_financiere, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5362 (class 0 OID 38233)
-- Dependencies: 229
-- Data for Name: types_appels_offres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.types_appels_offres (id_type_appel_offre, libelle_type_appel_offre, code_type_appel_offre, valeur_minimuim_type_appel_offre, valeur_maximuim_type_appel_offre, description_critere_type_appel_offre, actif_type_appel_offre, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at, parent_id, version_type_appel_offre, motif_modification_type_appel_offre) FROM stdin;
a0858897-bf8b-4260-8f5d-9079fcd64bc6	azert	AOCO	7000001.00	10000000.00	Description	f	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-05 16:57:07	2025-12-08 14:51:07	\N	\N	1	\N
a08b6484-1025-4c4f-acb5-4cb680c01ab2	azert	AOCO	7000002.00	10000000.00	Description	f	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-08 14:51:14	2025-12-09 12:54:42	\N	a0858897-bf8b-4260-8f5d-9079fcd64bc6	2	fgf
a07c9987-11a3-4050-b044-0490f7ce4113	Appel d’Offres avec Concours	AOC	500000.00	7000000.00	\N	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-01 06:22:03	2025-12-09 13:02:32	\N	\N	1	\N
a08d3de5-fde5-41ea-b7b5-bb9871fc5638	Appel d’Offres à Procédure d’Urgence	AOCO	7000002.00	10000000.00	Description	f	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-09 12:54:54	2025-12-09 13:04:50	\N	a08b6484-1025-4c4f-acb5-4cb680c01ab2	3	\N
a08d4175-e0a8-4b49-823f-eb23202cf35e	Appel d’Offres à Procédure d’Urgence	AOCO	7000001.00	10000000.00	Description	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-09 13:04:50	2025-12-09 13:04:50	\N	a08d3de5-fde5-41ea-b7b5-bb9871fc5638	4	Motif de modification
a073c14b-af1f-4b76-8cf4-0d5b8fb28dc7	Appel d’offres restreint	AOR	1000000.00	25000000.00	Seules les entreprises présélectionnées sont autorisées à soumissionner.\r\n➡️ Filtrage initial\r\n➡️ Plus rapide\r\n➡️ Garantit la qualité des soumissionnaires	f	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-11-26 20:50:46	2025-12-09 13:06:47	\N	\N	1	\N
a08d4228-5a3f-4ba1-a0ed-cbeb090140e4	Appel d’offres restreint	AOR	1000001.00	25000000.00	Seules les entreprises présélectionnées sont autorisées à soumissionner.\r\n➡️ Filtrage initial\r\n➡️ Plus rapide\r\n➡️ Garantit la qualité des soumissionnaires	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-09 13:06:47	2025-12-09 13:06:47	\N	a073c14b-af1f-4b76-8cf4-0d5b8fb28dc7	2	Motif de modification
a073b980-bbc7-440f-875e-4f216305a2e0	Appel d’offres ouvert	AOO	25000000.00	50000000.00	dfnjfggfkgfg	f	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-11-26 20:28:59	2025-12-09 13:07:11	\N	\N	1	\N
a08d424c-2d6f-4884-9f2d-32c277f3968f	Appel d’offres ouvert	AOO	25000001.00	50000000.00	dfnjfggfkgfg	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	2025-12-09 13:07:11	2025-12-09 13:07:11	\N	a073b980-bbc7-440f-875e-4f216305a2e0	2	Motif de modification
a08d428e-f0cf-4b74-a083-fdfb11105270	Appel d’Offres International	AOI	50000001.00	100000000.00	\N	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-09 13:07:54	2025-12-09 13:07:54	\N	\N	1	\N
a08d47aa-bcad-4af4-9d90-3031648bbafa	Appel d’Offres National	AON	100000001.00	150000000.00	\N	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-09 13:22:12	2025-12-09 13:22:12	\N	\N	1	\N
a08d4852-293b-475b-9291-17db5ae56e51	Demande de Renseignements et de Prix	DRP	250000.00	499999.00	\N	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-09 13:24:01	2025-12-09 13:24:01	\N	\N	1	\N
a08d48d4-6151-494a-bece-76f8241641e3	Demande de Cotation	DC	10000.00	499998.00	\N	t	1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	\N	\N	2025-12-09 13:25:27	2025-12-09 13:25:27	\N	\N	1	\N
\.


--
-- TOC entry 5356 (class 0 OID 38058)
-- Dependencies: 222
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, nom_complet, email, password, telephone_principal, telepone_secondaire, role_id, email_verified_at, statut, created_at, updated_at, deleted_at, created_by, updated_by, deleted_by) FROM stdin;
1a9e9bbb-c5b3-4f89-ba6d-46c93d947e7d	DJOBO NDRI	nfcdjobo@gmail.com	$2y$12$tYRVVN/X1MRp9Ld7KWQpyeWZCEH5dk5juIBL2oNTGrpGIx9Y.650C	+2250200000000	+225010100000	a073b928-64f6-48c7-b24f-9a2a3e46cd29	2025-12-10 08:15:48	1	2025-11-26 20:28:01	2025-12-10 08:15:48	\N	\N	\N	\N
\.


--
-- TOC entry 5516 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 136, true);


--
-- TOC entry 5116 (class 2606 OID 40292)
-- Name: alertes alertes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_pkey PRIMARY KEY (id);


--
-- TOC entry 5077 (class 2606 OID 38304)
-- Name: appels_offres appels_offres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_pkey PRIMARY KEY (id_appel_offre);


--
-- TOC entry 5098 (class 2606 OID 39990)
-- Name: banques banques_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_pkey PRIMARY KEY (id_banque);


--
-- TOC entry 5100 (class 2606 OID 40021)
-- Name: capacites_techniques capacites_techniques_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_pkey PRIMARY KEY (id_capacite_technique);


--
-- TOC entry 5079 (class 2606 OID 38338)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_pkey PRIMARY KEY (id_caracteristique_appel_offre);


--
-- TOC entry 5085 (class 2606 OID 38466)
-- Name: criteres_evaluations criteres_evaluations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_pkey PRIMARY KEY (id_critere_evaluation);


--
-- TOC entry 5087 (class 2606 OID 38591)
-- Name: documents documents_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_pkey PRIMARY KEY (id_document);


--
-- TOC entry 5114 (class 2606 OID 40253)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_pkey PRIMARY KEY (critere_evaluation_id, evaluation_id, prestatiare_id);


--
-- TOC entry 5091 (class 2606 OID 39959)
-- Name: evaluations evaluations_numero_evaluation_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_numero_evaluation_unique UNIQUE (numero_evaluation);


--
-- TOC entry 5093 (class 2606 OID 39957)
-- Name: evaluations evaluations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_pkey PRIMARY KEY (id_evaluation);


--
-- TOC entry 5104 (class 2606 OID 40085)
-- Name: evaluations_prestataires evaluations_prestataires_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_pkey PRIMARY KEY (id_evaluation_prestataire);


--
-- TOC entry 5047 (class 2606 OID 38120)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5049 (class 2606 OID 38122)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 5083 (class 2606 OID 38426)
-- Name: lots lots_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_pkey PRIMARY KEY (id_lot);


--
-- TOC entry 5033 (class 2606 OID 34852)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5106 (class 2606 OID 40117)
-- Name: paiements paiements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_pkey PRIMARY KEY (id_paiement);


--
-- TOC entry 5045 (class 2606 OID 38105)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 5062 (class 2606 OID 38175)
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 5064 (class 2606 OID 38177)
-- Name: permissions permissions_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_slug_unique UNIQUE (slug);


--
-- TOC entry 5051 (class 2606 OID 38135)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 5053 (class 2606 OID 38137)
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- TOC entry 5112 (class 2606 OID 40223)
-- Name: prestataires_lots prestataires_lots_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_pkey PRIMARY KEY (prestataire_id, lot_id, proforma_id);


--
-- TOC entry 5089 (class 2606 OID 39891)
-- Name: prestataires prestataires_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_pkey PRIMARY KEY (id_prestataire);


--
-- TOC entry 5081 (class 2606 OID 38382)
-- Name: proformas proformas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_pkey PRIMARY KEY (id_proforma);


--
-- TOC entry 5073 (class 2606 OID 38207)
-- Name: role_permissions role_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_pkey PRIMARY KEY (role_id, permission_id);


--
-- TOC entry 5035 (class 2606 OID 38055)
-- Name: roles roles_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_unique UNIQUE (name);


--
-- TOC entry 5037 (class 2606 OID 38053)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 5039 (class 2606 OID 38057)
-- Name: roles roles_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_slug_unique UNIQUE (slug);


--
-- TOC entry 5102 (class 2606 OID 40053)
-- Name: situations_financieres situations_financieres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_pkey PRIMARY KEY (id_situation_financiere);


--
-- TOC entry 5075 (class 2606 OID 38264)
-- Name: types_appels_offres types_appels_offres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_pkey PRIMARY KEY (id_type_appel_offre);


--
-- TOC entry 5066 (class 2606 OID 38163)
-- Name: permissions unique_permission_per_guard; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT unique_permission_per_guard UNIQUE (slug, guard_name);


--
-- TOC entry 5041 (class 2606 OID 38081)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 5043 (class 2606 OID 38079)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 5094 (class 1259 OID 39953)
-- Name: idx_evaluation_ao_lot; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_ao_lot ON public.evaluations USING btree (appel_offre_id, lot_id);


--
-- TOC entry 5095 (class 1259 OID 39954)
-- Name: idx_evaluation_prestataire; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_prestataire ON public.evaluations USING btree (prestataire_id, statut_evaluation);


--
-- TOC entry 5096 (class 1259 OID 39955)
-- Name: idx_evaluation_rang; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_evaluation_rang ON public.evaluations USING btree (rang);


--
-- TOC entry 5055 (class 1259 OID 38158)
-- Name: idx_permissions_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_category ON public.permissions USING btree (category);


--
-- TOC entry 5056 (class 1259 OID 38161)
-- Name: idx_permissions_complete; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_complete ON public.permissions USING btree (resource, action, guard_name, is_active);


--
-- TOC entry 5057 (class 1259 OID 38157)
-- Name: idx_permissions_guard_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_guard_active ON public.permissions USING btree (guard_name, is_active);


--
-- TOC entry 5058 (class 1259 OID 38156)
-- Name: idx_permissions_resource_action; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_resource_action ON public.permissions USING btree (resource, action);


--
-- TOC entry 5059 (class 1259 OID 38159)
-- Name: idx_permissions_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_slug ON public.permissions USING btree (slug);


--
-- TOC entry 5060 (class 1259 OID 38160)
-- Name: idx_permissions_system_active; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_permissions_system_active ON public.permissions USING btree (is_system, is_active);


--
-- TOC entry 5067 (class 1259 OID 38227)
-- Name: idx_role_permissions_actif_deleted; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_actif_deleted ON public.role_permissions USING btree (actif, deleted_at);


--
-- TOC entry 5068 (class 1259 OID 38225)
-- Name: idx_role_permissions_attribue_par; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_attribue_par ON public.role_permissions USING btree (attribue_par);


--
-- TOC entry 5069 (class 1259 OID 38226)
-- Name: idx_role_permissions_expiration; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_expiration ON public.role_permissions USING btree (expire_le);


--
-- TOC entry 5070 (class 1259 OID 38224)
-- Name: idx_role_permissions_perm_actif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_perm_actif ON public.role_permissions USING btree (permission_id, actif);


--
-- TOC entry 5071 (class 1259 OID 38223)
-- Name: idx_role_permissions_role_actif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_role_permissions_role_actif ON public.role_permissions USING btree (role_id, actif);


--
-- TOC entry 5054 (class 1259 OID 38133)
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- TOC entry 5107 (class 1259 OID 40226)
-- Name: prestataires_lots_idx_date_debut; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX prestataires_lots_idx_date_debut ON public.prestataires_lots USING btree (date_debut_reelle);


--
-- TOC entry 5108 (class 1259 OID 40227)
-- Name: prestataires_lots_idx_date_fin; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX prestataires_lots_idx_date_fin ON public.prestataires_lots USING btree (date_fin_reelle);


--
-- TOC entry 5109 (class 1259 OID 40224)
-- Name: prestataires_lots_idx_lot_statut; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX prestataires_lots_idx_lot_statut ON public.prestataires_lots USING btree (lot_id, statut_attribution);


--
-- TOC entry 5110 (class 1259 OID 40225)
-- Name: prestataires_lots_idx_prestataire_statut; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX prestataires_lots_idx_prestataire_statut ON public.prestataires_lots USING btree (prestataire_id, statut_attribution);


--
-- TOC entry 5202 (class 2606 OID 40276)
-- Name: alertes alertes_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5203 (class 2606 OID 40286)
-- Name: alertes alertes_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5204 (class 2606 OID 40281)
-- Name: alertes alertes_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alertes
    ADD CONSTRAINT alertes_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5133 (class 2606 OID 38288)
-- Name: appels_offres appels_offres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5134 (class 2606 OID 38298)
-- Name: appels_offres appels_offres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5135 (class 2606 OID 38283)
-- Name: appels_offres appels_offres_type_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_type_appel_offre_id_foreign FOREIGN KEY (type_appel_offre_id) REFERENCES public.types_appels_offres(id_type_appel_offre) ON DELETE RESTRICT;


--
-- TOC entry 5136 (class 2606 OID 38293)
-- Name: appels_offres appels_offres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.appels_offres
    ADD CONSTRAINT appels_offres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5170 (class 2606 OID 39974)
-- Name: banques banques_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5171 (class 2606 OID 39984)
-- Name: banques banques_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5172 (class 2606 OID 39969)
-- Name: banques banques_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE CASCADE;


--
-- TOC entry 5173 (class 2606 OID 39979)
-- Name: banques banques_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banques
    ADD CONSTRAINT banques_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5174 (class 2606 OID 40005)
-- Name: capacites_techniques capacites_techniques_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5175 (class 2606 OID 40015)
-- Name: capacites_techniques capacites_techniques_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5176 (class 2606 OID 40000)
-- Name: capacites_techniques capacites_techniques_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5177 (class 2606 OID 40010)
-- Name: capacites_techniques capacites_techniques_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.capacites_techniques
    ADD CONSTRAINT capacites_techniques_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5137 (class 2606 OID 38317)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_appel_offre_id_foreign FOREIGN KEY (appel_offre_id) REFERENCES public.appels_offres(id_appel_offre) ON DELETE CASCADE;


--
-- TOC entry 5138 (class 2606 OID 38322)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5139 (class 2606 OID 38332)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5140 (class 2606 OID 38339)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.caracteristiques_appels_offres(id_caracteristique_appel_offre) ON DELETE SET NULL;


--
-- TOC entry 5141 (class 2606 OID 38327)
-- Name: caracteristiques_appels_offres caracteristiques_appels_offres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.caracteristiques_appels_offres
    ADD CONSTRAINT caracteristiques_appels_offres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5151 (class 2606 OID 38450)
-- Name: criteres_evaluations criteres_evaluations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5152 (class 2606 OID 38460)
-- Name: criteres_evaluations criteres_evaluations_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5153 (class 2606 OID 38445)
-- Name: criteres_evaluations criteres_evaluations_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE CASCADE;


--
-- TOC entry 5154 (class 2606 OID 38455)
-- Name: criteres_evaluations criteres_evaluations_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.criteres_evaluations
    ADD CONSTRAINT criteres_evaluations_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5155 (class 2606 OID 38575)
-- Name: documents documents_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5156 (class 2606 OID 38585)
-- Name: documents documents_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5157 (class 2606 OID 38570)
-- Name: documents documents_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE SET NULL;


--
-- TOC entry 5158 (class 2606 OID 38580)
-- Name: documents documents_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5162 (class 2606 OID 39913)
-- Name: evaluations evaluations_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_appel_offre_id_foreign FOREIGN KEY (appel_offre_id) REFERENCES public.appels_offres(id_appel_offre) ON DELETE CASCADE;


--
-- TOC entry 5163 (class 2606 OID 39938)
-- Name: evaluations evaluations_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5164 (class 2606 OID 39948)
-- Name: evaluations evaluations_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5165 (class 2606 OID 39928)
-- Name: evaluations evaluations_evaluateur_principal_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_evaluateur_principal_id_foreign FOREIGN KEY (evaluateur_principal_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5166 (class 2606 OID 39918)
-- Name: evaluations evaluations_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE CASCADE;


--
-- TOC entry 5196 (class 2606 OID 40254)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5197 (class 2606 OID 40237)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_critere_evaluation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_critere_evaluation_id_foreign FOREIGN KEY (critere_evaluation_id) REFERENCES public.criteres_evaluations(id_critere_evaluation) ON DELETE SET NULL;


--
-- TOC entry 5198 (class 2606 OID 40264)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5199 (class 2606 OID 40242)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_evaluation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_evaluation_id_foreign FOREIGN KEY (evaluation_id) REFERENCES public.evaluations(id_evaluation) ON DELETE SET NULL;


--
-- TOC entry 5200 (class 2606 OID 40247)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_prestatiare_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_prestatiare_id_foreign FOREIGN KEY (prestatiare_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5201 (class 2606 OID 40259)
-- Name: evaluations_lots_prestataires evaluations_lots_prestataires_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_lots_prestataires
    ADD CONSTRAINT evaluations_lots_prestataires_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5167 (class 2606 OID 39923)
-- Name: evaluations evaluations_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE CASCADE;


--
-- TOC entry 5182 (class 2606 OID 40069)
-- Name: evaluations_prestataires evaluations_prestataires_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5183 (class 2606 OID 40079)
-- Name: evaluations_prestataires evaluations_prestataires_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5184 (class 2606 OID 40064)
-- Name: evaluations_prestataires evaluations_prestataires_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5185 (class 2606 OID 40074)
-- Name: evaluations_prestataires evaluations_prestataires_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations_prestataires
    ADD CONSTRAINT evaluations_prestataires_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5168 (class 2606 OID 39943)
-- Name: evaluations evaluations_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5169 (class 2606 OID 39933)
-- Name: evaluations evaluations_valide_par_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.evaluations
    ADD CONSTRAINT evaluations_valide_par_foreign FOREIGN KEY (valide_par) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5146 (class 2606 OID 38405)
-- Name: lots lots_appel_offre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_appel_offre_id_foreign FOREIGN KEY (appel_offre_id) REFERENCES public.appels_offres(id_appel_offre) ON DELETE CASCADE;


--
-- TOC entry 5147 (class 2606 OID 38410)
-- Name: lots lots_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5148 (class 2606 OID 38420)
-- Name: lots lots_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5149 (class 2606 OID 38427)
-- Name: lots lots_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.lots(id_lot) ON DELETE SET NULL;


--
-- TOC entry 5150 (class 2606 OID 38415)
-- Name: lots lots_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lots
    ADD CONSTRAINT lots_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5186 (class 2606 OID 40096)
-- Name: paiements paiements_banque_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_banque_id_foreign FOREIGN KEY (banque_id) REFERENCES public.banques(id_banque) ON DELETE SET NULL;


--
-- TOC entry 5187 (class 2606 OID 40101)
-- Name: paiements paiements_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5188 (class 2606 OID 40111)
-- Name: paiements paiements_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5189 (class 2606 OID 40106)
-- Name: paiements paiements_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paiements
    ADD CONSTRAINT paiements_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5121 (class 2606 OID 38164)
-- Name: permissions permissions_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5122 (class 2606 OID 38169)
-- Name: permissions permissions_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5159 (class 2606 OID 39875)
-- Name: prestataires prestataires_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5160 (class 2606 OID 39885)
-- Name: prestataires prestataires_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5190 (class 2606 OID 40207)
-- Name: prestataires_lots prestataires_lots_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5191 (class 2606 OID 40217)
-- Name: prestataires_lots prestataires_lots_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5192 (class 2606 OID 40197)
-- Name: prestataires_lots prestataires_lots_lot_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_lot_id_foreign FOREIGN KEY (lot_id) REFERENCES public.lots(id_lot) ON DELETE CASCADE;


--
-- TOC entry 5193 (class 2606 OID 40192)
-- Name: prestataires_lots prestataires_lots_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE CASCADE;


--
-- TOC entry 5194 (class 2606 OID 40202)
-- Name: prestataires_lots prestataires_lots_proforma_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_proforma_id_foreign FOREIGN KEY (proforma_id) REFERENCES public.proformas(id_proforma) ON DELETE CASCADE;


--
-- TOC entry 5195 (class 2606 OID 40212)
-- Name: prestataires_lots prestataires_lots_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires_lots
    ADD CONSTRAINT prestataires_lots_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5161 (class 2606 OID 39880)
-- Name: prestataires prestataires_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prestataires
    ADD CONSTRAINT prestataires_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5142 (class 2606 OID 38366)
-- Name: proformas proformas_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5143 (class 2606 OID 38376)
-- Name: proformas proformas_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5144 (class 2606 OID 38385)
-- Name: proformas proformas_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.proformas(id_proforma) ON DELETE SET NULL;


--
-- TOC entry 5145 (class 2606 OID 38371)
-- Name: proformas proformas_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proformas
    ADD CONSTRAINT proformas_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5123 (class 2606 OID 38218)
-- Name: role_permissions role_permissions_attribue_par_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_attribue_par_foreign FOREIGN KEY (attribue_par) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5124 (class 2606 OID 38191)
-- Name: role_permissions role_permissions_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5125 (class 2606 OID 38201)
-- Name: role_permissions role_permissions_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5126 (class 2606 OID 38213)
-- Name: role_permissions role_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 5127 (class 2606 OID 38208)
-- Name: role_permissions role_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 5128 (class 2606 OID 38196)
-- Name: role_permissions role_permissions_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5178 (class 2606 OID 40037)
-- Name: situations_financieres situations_financieres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5179 (class 2606 OID 40047)
-- Name: situations_financieres situations_financieres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5180 (class 2606 OID 40032)
-- Name: situations_financieres situations_financieres_prestataire_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_prestataire_id_foreign FOREIGN KEY (prestataire_id) REFERENCES public.prestataires(id_prestataire) ON DELETE SET NULL;


--
-- TOC entry 5181 (class 2606 OID 40042)
-- Name: situations_financieres situations_financieres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.situations_financieres
    ADD CONSTRAINT situations_financieres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5129 (class 2606 OID 38248)
-- Name: types_appels_offres types_appels_offres_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5130 (class 2606 OID 38258)
-- Name: types_appels_offres types_appels_offres_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5131 (class 2606 OID 40757)
-- Name: types_appels_offres types_appels_offres_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.types_appels_offres(id_type_appel_offre) ON DELETE SET NULL;


--
-- TOC entry 5132 (class 2606 OID 38253)
-- Name: types_appels_offres types_appels_offres_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.types_appels_offres
    ADD CONSTRAINT types_appels_offres_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5117 (class 2606 OID 38082)
-- Name: users users_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5118 (class 2606 OID 38092)
-- Name: users users_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5119 (class 2606 OID 38073)
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- TOC entry 5120 (class 2606 OID 38087)
-- Name: users users_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


-- Completed on 2025-12-10 16:20:53

--
-- PostgreSQL database dump complete
--

\unrestrict S1CrU6kjx54zmVkQIclRsflFtcrrokuFFtioOmmSS71PGgb99K4qNQbKwfV0MI7

